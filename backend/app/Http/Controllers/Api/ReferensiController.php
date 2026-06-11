<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KantorCabang;
use App\Models\Layanan;
use Illuminate\Http\JsonResponse;

class ReferensiController extends Controller
{
    /**
     * Return a list of active branch offices, ordered by province then name.
     */
    public function kantorCabang(): JsonResponse
    {
        $data = KantorCabang::where('is_active', true)
            ->orderBy('provinsi')
            ->orderBy('nama')
            ->get(['id', 'kode', 'nama', 'alamat', 'kota', 'provinsi', 'telepon', 'email']);

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * Return a list of active BPJS services.
     */
    public function layanan(): JsonResponse
    {
        $data = Layanan::where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'kode', 'nama', 'deskripsi']);

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }
}
