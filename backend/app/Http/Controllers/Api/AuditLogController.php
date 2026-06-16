<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Audit log API controller.
 *
 * Provides endpoints for querying and analyzing system audit logs.
 */
class AuditLogController extends Controller
{
    /**
     * Get audit logs with filtering.
     *
     * Query parameters:
     * - model_type: Filter by model class
     * - model_id: Filter by specific model instance
     * - action: Filter by action (created, updated, deleted, etc)
     * - user_id: Filter by user who made change
     * - start_date: Filter from date (YYYY-MM-DD)
     * - end_date: Filter to date (YYYY-MM-DD)
     * - limit: Number of results (default: 50, max: 500)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'model_type' => ['nullable', 'string'],
            'model_id'   => ['nullable', 'integer'],
            'action'     => ['nullable', 'string'],
            'user_id'    => ['nullable', 'integer'],
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date'],
            'limit'      => ['nullable', 'integer', 'min:1', 'max:500'],
        ]);

        $query = AuditLog::query();

        // Apply filters
        if ($request->has('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->has('model_id')) {
            $query->where('model_id', $request->model_id);
        }

        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59',
            ]);
        }

        $limit = min($request->integer('limit', 50), 500);

        $logs = $query->latest('created_at')
            ->paginate($limit);

        return response()->json([
            'success' => true,
            'data'    => $logs->items(),
            'meta'    => [
                'total'        => $logs->total(),
                'per_page'     => $logs->perPage(),
                'current_page' => $logs->currentPage(),
                'last_page'    => $logs->lastPage(),
            ],
        ]);
    }

    /**
     * Get audit logs for a specific model instance.
     *
     * @param string $modelType Model class name (URL encoded)
     * @param int $modelId Model instance ID
     * @return JsonResponse
     */
    public function modelHistory(string $modelType, int $modelId): JsonResponse
    {
        $logs = AuditLog::where('model_type', urldecode($modelType))
            ->where('model_id', $modelId)
            ->latest('created_at')
            ->limit(100)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $logs,
        ]);
    }

    /**
     * Get detailed view of a single audit log entry.
     *
     * @param AuditLog $auditLog
     * @return JsonResponse
     */
    public function show(AuditLog $auditLog): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'id'              => $auditLog->id,
                'action'          => $auditLog->action,
                'action_label'    => $auditLog->getActionLabel(),
                'model_type'      => $auditLog->model_type,
                'model_id'        => $auditLog->model_id,
                'user'            => $auditLog->user ? [
                    'id'    => $auditLog->user->id,
                    'email' => $auditLog->user->email,
                ] : null,
                'before_values'   => $auditLog->before_values,
                'after_values'    => $auditLog->after_values,
                'changed_fields'  => $auditLog->changed_fields,
                'differences'     => $auditLog->getDifferences(),
                'change_summary'  => $auditLog->getChangeSummary(),
                'reason'          => $auditLog->reason,
                'ip_address'      => $auditLog->ip_address,
                'request_id'      => $auditLog->request_id,
                'status'          => $auditLog->status,
                'error_message'   => $auditLog->error_message,
                'created_at'      => $auditLog->created_at,
            ],
        ]);
    }

    /**
     * Get audit summary for a model instance.
     *
     * @param string $modelType
     * @param int $modelId
     * @return JsonResponse
     */
    public function summary(string $modelType, int $modelId): JsonResponse
    {
        $logs = AuditLog::where('model_type', urldecode($modelType))
            ->where('model_id', $modelId)
            ->get();

        $actionCounts = $logs->groupBy('action')
            ->map(fn($group) => $group->count());

        $userActions = $logs->groupBy('user_email')
            ->map(fn($group) => $group->count());

        return response()->json([
            'success' => true,
            'data'    => [
                'total_changes'  => $logs->count(),
                'action_summary' => $actionCounts,
                'user_summary'   => $userActions,
                'first_change'   => $logs->min('created_at'),
                'last_change'    => $logs->max('created_at'),
            ],
        ]);
    }
}
