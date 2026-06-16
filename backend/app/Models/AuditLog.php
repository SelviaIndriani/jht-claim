<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Audit log model for tracking all system changes.
 *
 * Records before/after snapshots, user actions, timestamps, and IP addresses
 * for compliance and debugging purposes.
 *
 * @mixin IdeHelperAuditLog
 */
class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'action',           // created, updated, deleted, verified, submitted, etc
        'model_type',       // Model class name (e.g., KlaimJht, PesertaBpjs)
        'model_id',         // ID of the affected model
        'user_id',          // User who made the change (nullable for system events)
        'user_email',       // Email for easier identification
        'before_values',    // JSON snapshot of old values
        'after_values',     // JSON snapshot of new values
        'changed_fields',   // Array of field names that changed
        'reason',           // Why the change was made
        'ip_address',       // IP address of the requester
        'user_agent',       // Browser/client info
        'request_id',       // Correlation ID for tracing
        'status',           // success, failed, pending
        'error_message',    // If action failed
    ];

    protected $casts = [
        'before_values'   => 'array',
        'after_values'    => 'array',
        'changed_fields'  => 'array',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    protected $hidden = [
        'user_agent',       // Hide from API response
    ];

    /**
     * Get the user who made this change.
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get the affected model (polymorphic).
     */
    public function model()
    {
        return $this->morphTo('model');
    }

    /**
     * Scope: Get logs for specific model type.
     */
    public function scopeForModel($query, $modelClass)
    {
        return $query->where('model_type', $modelClass);
    }

    /**
     * Scope: Get logs by action type.
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: Get logs by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Get logs within date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get human-readable action label.
     */
    public function getActionLabel(): string
    {
        return match($this->action) {
            'created' => 'Dibuat',
            'updated' => 'Diubah',
            'deleted' => 'Dihapus',
            'verified' => 'Diverifikasi',
            'submitted' => 'Diajukan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => $this->action,
        };
    }

    /**
     * Get summary of what changed.
     */
    public function getChangeSummary(): string
    {
        if (empty($this->changed_fields)) {
            return 'No changes';
        }

        return implode(', ', $this->changed_fields);
    }

    /**
     * Get difference between before and after values.
     */
    public function getDifferences(): array
    {
        $differences = [];

        if (!$this->before_values || !$this->after_values) {
            return $differences;
        }

        foreach ($this->changed_fields ?? [] as $field) {
            $before = $this->before_values[$field] ?? null;
            $after = $this->after_values[$field] ?? null;

            if ($before !== $after) {
                $differences[$field] = [
                    'before' => $before,
                    'after' => $after,
                ];
            }
        }

        return $differences;
    }
}
