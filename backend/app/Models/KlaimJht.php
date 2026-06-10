<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class KlaimJht extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'klaim_jht';

    protected $fillable = [
        'no_klaim', 
        'no_bpjs', 
        'nik', 
        'nama_lengkap', 
        'nama_ibu_kandung',
        'tempat_lahir', 
        'tanggal_lahir', 
        'email',
        'sebab_klaim', 
        'layanan_dipilih', 
        'cara_konfirmasi', 
        'kantor_cabang_id',
        'foto_ktp', 
        'pas_foto', 
        'status', 
        'submitted_at',
    ];

    protected $casts = [
        'tanggal_lahir'   => 'date',
        'layanan_dipilih' => 'array',
        'submitted_at'    => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['no_klaim', 'no_bpjs', 'nik', 'sebab_klaim', 'status', 'cara_konfirmasi'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => "Klaim JHT {$eventName}");
    }

    public function kantorCabang()
    {
        return $this->belongsTo(KantorCabang::class, 'kantor_cabang_id');
    }
}
