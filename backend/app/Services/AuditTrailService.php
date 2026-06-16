<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Utilities\DataMasking;
use Illuminate\Database\Eloquent\Model;

/**
 * Audit trail service for recording and querying system changes.
 *
 * Provides centralized logging of all model changes with before/after snapshots,
 * user tracking, and comprehensive filtering capabilities.
 */
class AuditTrailService
{
    /**
     * Log a model action (create, update, delete).
     *
     * @param Model $model The affected model
     * @param string $action Action type (created, updated, deleted, etc)
     * @param array $beforeValues Original values (for updates/deletes)
     * @param array $afterValues New values (for creates/updates)
     * @param string|null $reason Why the action was performed
     * @return AuditLog Created audit log entry
     */
    public function log(
        Model $model,
        string $action,
        array $beforeValues = [],
        array $afterValues = [],
        ?string $reason = null
    ): AuditLog {
        $changedFields = $this->getChangedFields($beforeValues, $afterValues);

        return AuditLog::create([
            'action'         => $action,
            'model_type'     => $model::class,
            'model_id'       => $model->getKey(),
            'user_id'        => auth()->id(),
            'user_email'     => auth()?->user()?->email,
            'before_values'  => DataMasking::mask($beforeValues),
            'after_values'   => DataMasking::mask($afterValues),
            'changed_fields' => $changedFields,
            'reason'         => $reason,
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
            'request_id'     => request()->attributes->get('request_id'),
            'status'         => 'success',
        ]);
    }

    /**
     * Log a failed action.
     *
     * @param string $action Action type
     * @param string $modelType Model class name
     * @param int|null $modelId Model ID (if known)
     * @param string $errorMessage Error description
     * @return AuditLog Created audit log entry
     */
    public function logFailure(
        string $action,
        string $modelType,
        ?int $modelId = null,
        string $errorMessage = ''
    ): AuditLog {
        return AuditLog::create([
            'action'         => $action,
            'model_type'     => $modelType,
            'model_id'       => $modelId,
            'user_id'        => auth()->id(),
            'user_email'     => auth()?->user()?->email,
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
            'request_id'     => request()->attributes->get('request_id'),
            'status'         => 'failed',
            'error_message'  => $errorMessage,
        ]);
    }

    /**
     * Get audit logs for a model instance.
     *
     * @param Model $model
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getModelHistory(Model $model, int $limit = 50)
    {
        return AuditLog::forModel($model::class)
            ->where('model_id', $model->getKey())
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user's actions within date range.
     *
     * @param int $userId
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserActions($userId, $startDate, $endDate)
    {
        return AuditLog::byUser($userId)
            ->dateRange($startDate, $endDate)
            ->latest('created_at')
            ->get();
    }

    /**
     * Get changes to specific model type.
     *
     * @param string $modelClass Model class name
     * @param array $filters Optional filters: action, startDate, endDate, userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getModelChanges(string $modelClass, array $filters = [])
    {
        $query = AuditLog::forModel($modelClass);

        if (isset($filters['action'])) {
            $query->byAction($filters['action']);
        }

        if (isset($filters['userId'])) {
            $query->byUser($filters['userId']);
        }

        if (isset($filters['startDate']) && isset($filters['endDate'])) {
            $query->dateRange($filters['startDate'], $filters['endDate']);
        }

        return $query->latest('created_at')->get();
    }

    /**
     * Get which fields changed in a model.
     *
     * @param array $before Before values
     * @param array $after After values
     * @return array Changed field names
     */
    private function getChangedFields(array $before, array $after): array
    {
        $changed = [];

        // For new records (create)
        if (empty($before)) {
            return array_keys($after);
        }

        // For updates
        foreach ($after as $field => $value) {
            $beforeValue = $before[$field] ?? null;
            if ($value !== $beforeValue) {
                $changed[] = $field;
            }
        }

        return $changed;
    }
}
