<?php

namespace App\Traits;

use App\Services\AuditTrailService;
use Illuminate\Database\Eloquent\Model;

/**
 * Automatically audit model changes (create, update, delete).
 *
 * Attach this trait to any model that should have automatic audit logging.
 * Tracks before/after snapshots for updates, captures creates/deletes.
 *
 * Usage:
 *   class KlaimJht extends Model {
 *       use AuditsChanges;
 *   }
 */
trait AuditsChanges
{
    public static function bootAuditsChanges(): void
    {
        static::created(function (Model $model) {
            app(AuditTrailService::class)->log(
                $model,
                'created',
                [],
                $model->getAttributes()
            );
        });

        static::updated(function (Model $model) {
            $original = $model->getOriginal();
            $current = $model->getAttributes();

            if ($original !== $current) {
                app(AuditTrailService::class)->log(
                    $model,
                    'updated',
                    $original,
                    $current
                );
            }
        });

        static::deleted(function (Model $model) {
            app(AuditTrailService::class)->log(
                $model,
                'deleted',
                $model->getOriginal(),
                []
            );
        });
    }
}
