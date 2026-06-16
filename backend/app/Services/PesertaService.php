<?php

namespace App\Services;

use App\Models\Layanan;
use App\Repositories\PesertaRepository;
use App\Traits\ValidatesInput;
use App\Utilities\LogActivity;

class PesertaService
{
    use ValidatesInput;

    public function __construct(
        protected PesertaRepository $pesertaRepository
    ) {}

    public function verifikasi(string $noBpjs, string $nik, string $email): ?array
    {
        $member = $this->pesertaRepository->findActiveByCombination($noBpjs, $nik);

        if (!$member) {
            LogActivity::warning('peserta_verification_failed_not_found', [
                'no_bpjs' => $noBpjs,
                'nik'     => $nik,
            ]);
            return null;
        }

        if (!$this->stringMatch($email, $member->email ?? '')) {
            LogActivity::warning('peserta_verification_failed_email_mismatch', [
                'no_bpjs'       => $noBpjs,
                'nik'           => $nik,
                'provided_email' => $email,
            ]);
            return null;
        }

        $layanan = Layanan::whereIn('id', $member->layanan_ids ?? [])
            ->where('is_active', true)
            ->get(['id', 'kode', 'nama', 'deskripsi']);

        LogActivity::info('peserta_verified', [
            'peserta_id' => $member->id,
            'no_bpjs'    => $noBpjs,
        ]);

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
