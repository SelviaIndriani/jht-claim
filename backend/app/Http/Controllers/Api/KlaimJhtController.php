<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KlaimJhtRequest;
use App\Mail\KlaimJhtMail;
use App\Models\KantorCabang;
use App\Models\KlaimJht;
use App\Models\Layanan;
use App\Models\PesertaBpjs;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KlaimJhtController extends Controller
{
    public function store(KlaimJhtRequest $request): JsonResponse
    {
        // Step 1: Cek kombinasi no_bpjs + nik ada di DB
        $member = PesertaBpjs::where('no_bpjs', $request->no_bpjs)
            ->where('nik', $request->nik)
            ->where('is_active', true)
            ->first();

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

        // Step 2: Validasi silang field yang diisi user dengan data di DB
        $fieldErrors = [];

        if (strtolower(trim($request->nama_lengkap)) !== strtolower(trim($member->nama_lengkap))) {
            $fieldErrors['nama_lengkap'] = ['Nama lengkap tidak sesuai dengan data kepesertaan.'];
        }

        if (strtolower(trim($request->nama_ibu_kandung)) !== strtolower(trim($member->nama_ibu_kandung ?? ''))) {
            $fieldErrors['nama_ibu_kandung'] = ['Nama ibu kandung tidak sesuai dengan data kepesertaan.'];
        }

        if (strtolower(trim($request->tempat_lahir)) !== strtolower(trim($member->tempat_lahir))) {
            $fieldErrors['tempat_lahir'] = ['Tempat lahir tidak sesuai dengan data kepesertaan.'];
        }

        if ($request->tanggal_lahir !== $member->tanggal_lahir->format('Y-m-d')) {
            $fieldErrors['tanggal_lahir'] = ['Tanggal lahir tidak sesuai dengan data kepesertaan.'];
        }

        if (strtolower(trim($request->email)) !== strtolower(trim($member->email ?? ''))) {
            $fieldErrors['email'] = ['Alamat email tidak sesuai dengan data kepesertaan.'];
        }

        if (!empty($fieldErrors)) {
            return response()->json([
                'success' => false,
                'message' => 'Data yang Anda masukkan tidak sesuai dengan data kepesertaan BPJS Ketenagakerjaan. Harap periksa kembali.',
                'errors'  => $fieldErrors,
            ], 422);
        }

        // Step 3: Pastikan layanan yang dipilih sesuai dengan kepesertaan
        $memberServices    = $member->layanan_ids ?? [];
        $requestedServices = $request->layanan_dipilih;
        $invalidServices   = array_diff($requestedServices, $memberServices);

        if (!empty($invalidServices)) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat jenis layanan yang tidak sesuai dengan data kepesertaan Anda.',
                'errors'  => [
                    'layanan_dipilih' => ['Layanan yang dipilih tidak sesuai dengan kepesertaan Anda.'],
                ],
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Upload foto E-KTP
            $fotoKtpPath = null;
            if ($request->hasFile('foto_ktp')) {
                $fotoKtpPath = $request->file('foto_ktp')->store('klaim/ktp', 'public');
            }

            // Upload pas foto
            $pasFotoPath = null;
            if ($request->hasFile('pas_foto')) {
                $pasFotoPath = $request->file('pas_foto')->store('klaim/pasfoto', 'public');
            }

            // Generate nomor klaim unik
            $noKlaim = 'JHT-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            // Simpan klaim — hanya field yang ada di form
            $klaim = KlaimJht::create([
                'no_klaim'         => $noKlaim,
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

            // Ambil data layanan untuk email
            $layananData = Layanan::whereIn('id', $request->layanan_dipilih)
                ->get(['id', 'kode', 'nama', 'deskripsi'])
                ->toArray();

            // Ambil data kantor cabang untuk email (jika ada)
            $kantorData = null;
            if ($request->kantor_cabang_id) {
                $kantorData = KantorCabang::find(
                    $request->kantor_cabang_id,
                    ['id', 'nama', 'alamat', 'kota', 'provinsi', 'telepon']
                )?->toArray();
            }

            // Siapkan payload email
            $emailPayload = [
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
                'layanan'          => $layananData,
                'kantor_cabang'    => $kantorData,
            ];

            // Kirim email konfirmasi
            Mail::to($klaim->email)->send(new KlaimJhtMail($emailPayload));

            // Log aktivitas pengajuan klaim
            activity('klaim_jht')
                ->performedOn($klaim)
                ->withProperties([
                    'no_klaim'        => $klaim->no_klaim,
                    'no_bpjs'         => $klaim->no_bpjs,
                    'sebab_klaim'     => $klaim->sebab_klaim,
                    'cara_konfirmasi' => $klaim->cara_konfirmasi,
                    'ip_address'      => $request->ip(),
                ])
                ->log('Pengajuan klaim JHT berhasil dibuat');

            DB::commit();

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

        } catch (\Exception $e) {
            DB::rollBack();

            // Hapus file yang terupload jika transaksi gagal
            if ($fotoKtpPath) Storage::disk('public')->delete($fotoKtpPath);
            if ($pasFotoPath) Storage::disk('public')->delete($pasFotoPath);

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
            'data'    => array_merge($klaim->toArray(), ['layanan' => $layanan]),
        ]);
    }
}
