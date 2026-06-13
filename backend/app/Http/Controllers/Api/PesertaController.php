<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyPesertaRequest;
use App\Services\PesertaService;
use Illuminate\Http\JsonResponse;

class PesertaController extends Controller
{
    public function __construct(private readonly PesertaService $pesertaService) {}

    public function verifikasi(VerifyPesertaRequest $request): JsonResponse
    {
        $result = $this->pesertaService->verifikasi(
            $request->no_bpjs,
            $request->nik,
            $request->email
        );

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Kombinasi Nomor KPJ, NIK, dan email tidak ditemukan. Pastikan data yang Anda masukkan sudah benar.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data kepesertaan ditemukan.',
            'data'    => $result,
        ]);
    }
}
