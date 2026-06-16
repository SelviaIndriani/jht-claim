<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KlaimJhtRequest;
use App\Models\KlaimJht;
use App\Models\Layanan;
use App\Services\KlaimJhtService;
use Illuminate\Http\JsonResponse;

class KlaimJhtController extends Controller
{
    public function __construct(private readonly KlaimJhtService $klaimService) {}

    public function store(KlaimJhtRequest $request): JsonResponse
    {
        // Verify member exists and is active
        $member = $this->klaimService->findActiveMember($request->no_bpjs, $request->nik);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Kombinasi Nomor KPJ dan NIK tidak ditemukan dalam sistem.',
                'errors'  => [
                    'no_bpjs' => ['Nomor KPJ tidak terdaftar dengan NIK yang diberikan.'],
                    'nik'     => ['NIK tidak cocok dengan Nomor KPJ yang diberikan.'],
                ],
            ], 422);
        }

        // Cross-validate submitted fields against membership data in DB
        $fieldErrors = $this->klaimService->validateMemberFields($request, $member);

        if (!empty($fieldErrors)) {
            return response()->json([
                'success' => false,
                'message' => 'Data yang Anda masukkan tidak sesuai dengan data kepesertaan BPJS Ketenagakerjaan.',
                'errors'  => $fieldErrors,
            ], 422);
        }

        // Ensure requested services match the member's entitlements
        $invalidLayanan = $this->klaimService->validateLayanan($request->layanan_dipilih, $member);

        if (!empty($invalidLayanan)) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat jenis layanan yang tidak sesuai dengan kepesertaan Anda.',
                'errors'  => ['layanan_dipilih' => ['Layanan yang dipilih tidak sesuai dengan kepesertaan Anda.']],
            ], 422);
        }

        try {
            $klaim = $this->klaimService->submitKlaim($request);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan klaim JHT berhasil dikirim. Email konfirmasi telah dikirimkan ke ' . $klaim->email,
                'data'    => [
                    'no_klaim'     => $klaim->no_klaim,
                    'status'       => $klaim->status,
                    'submitted_at' => $klaim->submitted_at,
                    'email'        => $klaim->email,
                ],
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses klaim. Silakan coba lagi.',
            ], 500);
        }
    }

    public function show(string $noKlaim): JsonResponse
    {
        $klaim = KlaimJht::where('no_klaim', $noKlaim)
            ->with('kantorCabang')
            ->first();

        if (!$klaim) {
            return response()->json([
                'success' => false,
                'message' => 'Data klaim tidak ditemukan.',
            ], 404);
        }

        $layanan = Layanan::whereIn('id', $klaim->layanan_dipilih ?? [])->get(['id', 'kode', 'nama']);

        return response()->json([
            'success' => true,
            'data'    => [
                'no_klaim'         => $klaim->no_klaim,
                'no_bpjs'          => $klaim->no_bpjs,
                'nik'              => $klaim->nik,
                'nama_lengkap'     => $klaim->nama_lengkap,
                'email'            => $klaim->email,
                'sebab_klaim'      => $klaim->sebab_klaim,
                'cara_konfirmasi'  => $klaim->cara_konfirmasi,
                'status'           => $klaim->status,
                'submitted_at'     => $klaim->submitted_at,
                'layanan'          => $layanan,
            ],
        ]);
    }
}
