<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\PesertaBpjs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PesertaController extends Controller
{
    /**
     * Verifikasi kombinasi no_bpjs + nik.
     * Mengembalikan daftar layanan yang berhak diterima peserta jika kombinasi valid.
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

        $member = PesertaBpjs::where('no_bpjs', $request->no_bpjs)
            ->where('nik', $request->nik)
            ->where('is_active', true)
            ->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Kombinasi Nomor KPJ dan NIK tidak ditemukan dalam sistem. Pastikan data yang Anda masukkan sudah benar.',
            ], 404);
        }

        // Ambil layanan yang berhak diterima peserta berdasarkan layanan_ids
        $layanan = Layanan::whereIn('id', $member->layanan_ids ?? [])
            ->where('is_active', true)
            ->get(['id', 'kode', 'nama', 'deskripsi']);

        return response()->json([
            'success' => true,
            'message' => 'Data kepesertaan ditemukan.',
            'data'    => [
                'peserta' => [
                    'no_bpjs'          => $member->no_bpjs,
                    'nik'              => $member->nik,
                    'nama_lengkap'     => $member->nama_lengkap,
                    'nama_ibu_kandung' => $member->nama_ibu_kandung,
                    'tempat_lahir'     => $member->tempat_lahir,
                    'tanggal_lahir'    => $member->tanggal_lahir->format('Y-m-d'),
                    'email'            => $member->email,
                ],
                'layanan' => $layanan,
            ],
        ]);
    }
}
