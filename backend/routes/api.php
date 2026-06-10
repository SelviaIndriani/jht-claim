<?php

use App\Http\Controllers\Api\KlaimJhtController;
use App\Http\Controllers\Api\PesertaController;
use App\Http\Controllers\Api\ReferensiController;
use Illuminate\Support\Facades\Route;

// Reference data — publicly accessible
Route::prefix('referensi')->group(function () {
    Route::get('/kantor-cabang', [ReferensiController::class, 'kantorCabang']); // branch offices
    Route::get('/layanan', [ReferensiController::class, 'layanan']);             // available services
});

// Member verification
Route::post('/peserta/verifikasi', [PesertaController::class, 'verifikasi']);

// JHT Claim
Route::post('/klaim', [KlaimJhtController::class, 'store']);
Route::get('/klaim/{claimNumber}', [KlaimJhtController::class, 'show']);
