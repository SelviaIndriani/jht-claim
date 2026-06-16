<?php

namespace App\Models;

use App\Traits\AuditsChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * @mixin IdeHelperPesertaBpjs
 */
class PesertaBpjs extends Model
{
    use SoftDeletes, LogsActivity, AuditsChanges;

    protected $table = 'peserta_bpjs';

    protected $fillable = [
        'no_bpjs', 
        'nik', 
        'nama_lengkap', 
        'nama_ibu_kandung',
        'tempat_lahir', 
        'tanggal_lahir', 
        'email', 
        'layanan_ids', 
        'is_active',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'layanan_ids'   => 'array',
        'is_active'     => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['no_bpjs', 'nik', 'nama_lengkap', 'is_active'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => "Data peserta BPJS {$eventName}");
    }
}
