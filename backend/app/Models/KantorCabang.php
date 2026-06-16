<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperKantorCabang
 */
class KantorCabang extends Model
{
    protected $table = 'kantor_cabang';

    protected $fillable = [
        'kode', 
        'nama', 
        'alamat', 
        'kota', 
        'provinsi', 
        'telepon', 
        'email', 
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all JHT claims associated with this branch office.
     */
    public function klaimJht()
    {
        return $this->hasMany(KlaimJht::class, 'kantor_cabang_id');
    }
}
