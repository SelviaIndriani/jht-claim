<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'layanan';

    protected $fillable = [
        'kode', 
        'nama', 
        'deskripsi', 
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
