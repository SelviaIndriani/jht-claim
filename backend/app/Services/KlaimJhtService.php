<?php

namespace App\Services;

use App\Mail\KlaimJhtMail;
use App\Models\KantorCabang;
use App\Models\KlaimJht;
use App\Models\Layanan;
use App\Models\PesertaBpjs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KlaimJhtService
{
    /**
     * Find an active member by no_bpjs + nik combination.
     * Returns the member model or null if not found.
     */
    public function findActiveMember(string $noBpjs, string $nik): ?PesertaBpjs
    {
        return PesertaBpjs::where('no_bpjs', $noBpjs)
            ->where('nik', $nik)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Cross-validate submitted form fields against the member's data in the database.
     * Returns an array of field errors, empty if all fields are valid.
     */
    public function validateMemberFields(Request $request, PesertaBpjs $member): array
    {
        $errors = [];

        if (strtolower(trim($request->nama_lengkap)) !== strtolower(trim($member->nama_lengkap))) {
            $errors['nama_lengkap'] = ['Nama lengkap tidak sesuai dengan data kepesertaan.'];
        }

        if (strtolower(trim($request->nama_ibu_kandung)) !== strtolower(trim($member->nama_ibu_kandung ?? ''))) {
            $errors['nama_ibu_kandung'] = ['Nama ibu kandung tidak sesuai dengan data kepesertaan.'];
        }

        if (strtolower(trim($request->tempat_lahir)) !== strtolower(trim($member->tempat_lahir))) {
            $errors['tempat_lahir'] = ['Tempat lahir tidak sesuai dengan data kepesertaan.'];
        }

        if ($request->tanggal_lahir !== $member->tanggal_lahir->format('Y-m-d')) {
            $errors['tanggal_lahir'] = ['Tanggal lahir tidak sesuai dengan data kepesertaan.'];
        }

        if (strtolower(trim($request->email)) !== strtolower(trim($member->email ?? ''))) {
            $errors['email'] = ['Alamat email tidak sesuai dengan data kepesertaan.'];
        }

        return $errors;
    }

    /**
     * Validate that the requested services match the member's entitlements.
     * Returns an array of invalid service IDs, empty if all are valid.
     */
    public function validateLayanan(array $requestedLayanan, PesertaBpjs $member): array
    {
        return array_diff($requestedLayanan, $member->layanan_ids ?? []);
    }

    /**
     * Process a JHT claim submission: upload files, persist to DB,
     * send confirmation email, and log the activity.
     * Returns the persisted KlaimJht model.
     *
     * @throws \Exception
     */
    public function submitKlaim(Request $request): KlaimJht
    {
        $fotoKtpPath = null;
        $pasFotoPath = null;

        DB::beginTransaction();
        try {
            $fotoKtpPath = $this->uploadFile($request, 'foto_ktp', 'klaim/ktp');
            $pasFotoPath = $this->uploadFile($request, 'pas_foto', 'klaim/pasfoto');

            $klaim = KlaimJht::create([
                'no_klaim'         => $this->generateNoKlaim(),
                'no_bpjs'          => $request->no_bpjs,
                'nik'              => $request->nik,
                'nama_lengkap'     => $request->nama_lengkap,
                'nama_ibu_kandung' => $request->nama_ibu_kandung,
                'tempat_lahir'     => $request->tempat_lahir,
                'tanggal_lahir'    => $request->tanggal_lahir,
                'email'            => $request->email,
                'sebab_klaim'      => $request->sebab_klaim,
                'layanan_dipilih'  => $request->layanan_dipilih,
                'cara_konfirmasi'  => $request->cara_konfirmasi,
                'kantor_cabang_id' => $request->kantor_cabang_id,
                'foto_ktp'         => $fotoKtpPath,
                'pas_foto'         => $pasFotoPath,
                'status'           => 'pending',
                'submitted_at'     => now(),
            ]);

            $this->sendConfirmationEmail($klaim, $request);
            $this->logActivity($klaim, $request);

            DB::commit();

            return $klaim;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->cleanupUploadedFiles($fotoKtpPath, $pasFotoPath);
            throw $e;
        }
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function uploadFile(Request $request, string $field, string $directory): ?string
    {
        if ($request->hasFile($field)) {
            return $request->file($field)->store($directory, 'public');
        }

        return null;
    }

    private function generateNoKlaim(): string
    {
        return 'JHT-' . date('Ymd') . '-' . strtoupper(Str::random(6));
    }

    private function sendConfirmationEmail(KlaimJht $klaim, Request $request): void
    {
        $layanan = Layanan::whereIn('id', $request->layanan_dipilih)
            ->get(['id', 'kode', 'nama', 'deskripsi'])
            ->toArray();

        $kantorCabang = $request->kantor_cabang_id
            ? KantorCabang::find($request->kantor_cabang_id, ['id', 'nama', 'alamat', 'kota', 'provinsi', 'telepon'])?->toArray()
            : null;

        Mail::to($klaim->email)->send(new KlaimJhtMail([
            'no_klaim'         => $klaim->no_klaim,
            'no_bpjs'          => $klaim->no_bpjs,
            'nik'              => $klaim->nik,
            'nama_lengkap'     => $klaim->nama_lengkap,
            'nama_ibu_kandung' => $klaim->nama_ibu_kandung,
            'tempat_lahir'     => $klaim->tempat_lahir,
            'tanggal_lahir'    => $klaim->tanggal_lahir,
            'email'            => $klaim->email,
            'sebab_klaim'      => $klaim->sebab_klaim,
            'cara_konfirmasi'  => $klaim->cara_konfirmasi,
            'submitted_at'     => $klaim->submitted_at,
            'layanan'          => $layanan,
            'kantor_cabang'    => $kantorCabang,
        ]));
    }

    private function logActivity(KlaimJht $klaim, Request $request): void
    {
        activity('klaim_jht')
            ->performedOn($klaim)
            ->withProperties([
                'no_klaim'        => $klaim->no_klaim,
                'no_bpjs'         => $klaim->no_bpjs,
                'sebab_klaim'     => $klaim->sebab_klaim,
                'cara_konfirmasi' => $klaim->cara_konfirmasi,
                'ip_address'      => $request->ip(),
            ])
            ->log('JHT claim submission created successfully');
    }

    private function cleanupUploadedFiles(?string ...$paths): void
    {
        foreach ($paths as $path) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}
