<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PesertaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PesertaController extends Controller
{
    public function __construct(private readonly PesertaService $pesertaService) {}

    /**
     * Verify the no_bpjs + nik combination.
     * Returns member data and eligible services if the combination is valid.
     */
    public function verifikasi(Request $request): JsonResponse
    {
        $request->validate([
            'no_bpjs' => ['required', 'string', 'regex:/^\d{11}$/'],
            'nik'     => ['required', 'string', 'regex:/^\d{16}$/'],
        ], [
            'no_bpjs.required' => 'Nomor KPJ wajib diisi.',
            'no_bpjs.regex'    => 'Nomor KPJ harus tepat 11 digit.',
            'nik.required'     => 'NIK wajib diisi.',
            'nik.regex'        => 'NIK harus tepat 16 digit.',
        ]);

        $result = $this->pesertaService->verifikasi($request->no_bpjs, $request->nik);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Kombinasi Nomor KPJ dan NIK tidak ditemukan. Pastikan data yang Anda masukkan sudah benar.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data kepesertaan ditemukan.',
            'data'    => $result,
        ]);
    }
}
