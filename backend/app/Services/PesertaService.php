<?php

namespace App\Services;

use App\Models\Layanan;
use App\Models\PesertaBpjs;

class PesertaService
{
    /**
     * Verify the no_bpjs + nik combination.
     * Returns ['peserta' => ..., 'layanan' => ...] if valid, or null if not found.
     */
    public function verifikasi(string $noBpjs, string $nik): ?array
    {
        $member = PesertaBpjs::where('no_bpjs', $noBpjs)
            ->where('nik', $nik)
            ->where('is_active', true)
            ->first();

        if (!$member) {
            return null;
        }

        $layanan = Layanan::whereIn('id', $member->layanan_ids ?? [])
            ->where('is_active', true)
            ->get(['id', 'kode', 'nama', 'deskripsi']);

        return [
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
        ];
    }
}
