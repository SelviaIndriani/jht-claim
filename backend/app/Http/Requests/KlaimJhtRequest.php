<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class KlaimJhtRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sebabKlaimValues = implode(',', array_column(\App\Enums\SebabKlaim::cases(), 'value'));
        $caraKonfirmasiValues = implode(',', array_column(\App\Enums\CaraKonfirmasi::cases(), 'value'));

        return [
            'no_bpjs'          => ['required', 'string', 'regex:/^\d{11}$/'],
            'nik'              => ['required', 'string', 'regex:/^\d{16}$/', 'regex:/^[1-9]\d{15}$/'],
            'nama_lengkap'     => ['required', 'string', 'min:3', 'max:100'],
            'nama_ibu_kandung' => ['required', 'string', 'min:3', 'max:100'],
            'tempat_lahir'     => ['required', 'string', 'min:2', 'max:50'],
            'tanggal_lahir'    => ['required', 'date', 'before:today'],
            'email'            => ['required', 'email:rfc,dns'],
            'sebab_klaim'      => ["required", "in:{$sebabKlaimValues}"],
            'layanan_dipilih'  => ['required', 'array', 'min:1'],
            'layanan_dipilih.*'=> ['integer', 'exists:layanan,id'],
            'cara_konfirmasi'  => ["required", "in:{$caraKonfirmasiValues}"],
            'kantor_cabang_id' => ['nullable', 'required_if:cara_konfirmasi,datang_kantor', 'exists:kantor_cabang,id'],
            'foto_ktp'         => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'pas_foto'         => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'no_bpjs.required'             => 'Nomor KPJ wajib diisi.',
            'no_bpjs.regex'                => 'Nomor KPJ harus terdiri dari tepat 11 digit angka.',
            'nik.required'                 => 'NIK wajib diisi.',
            'nik.regex'                    => 'NIK harus terdiri dari tepat 16 digit angka yang valid.',
            'nama_lengkap.required'        => 'Nama lengkap wajib diisi.',
            'nama_lengkap.min'             => 'Nama lengkap minimal 3 karakter.',
            'nama_ibu_kandung.required'    => 'Nama ibu kandung wajib diisi.',
            'nama_ibu_kandung.min'         => 'Nama ibu kandung minimal 3 karakter.',
            'tempat_lahir.required'        => 'Tempat lahir wajib diisi.',
            'tanggal_lahir.required'       => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.before'         => 'Tanggal lahir harus sebelum hari ini.',
            'email.required'               => 'Alamat email wajib diisi.',
            'email.email'                  => 'Format alamat email tidak valid.',
            'sebab_klaim.required'         => 'Sebab klaim wajib dipilih.',
            'sebab_klaim.in'               => 'Sebab klaim tidak valid.',
            'layanan_dipilih.required'     => 'Jenis layanan wajib dipilih minimal satu.',
            'layanan_dipilih.min'          => 'Pilih minimal satu jenis layanan.',
            'cara_konfirmasi.required'     => 'Cara konfirmasi klaim wajib dipilih.',
            'cara_konfirmasi.in'           => 'Cara konfirmasi tidak valid.',
            'kantor_cabang_id.required_if' => 'Kantor cabang wajib dipilih jika konfirmasi datang ke kantor.',
            'kantor_cabang_id.exists'      => 'Kantor cabang yang dipilih tidak ditemukan.',
            'foto_ktp.required'            => 'Foto E-KTP wajib diunggah.',
            'foto_ktp.mimes'               => 'Foto E-KTP harus berformat JPG atau PNG.',
            'foto_ktp.max'                 => 'Ukuran foto E-KTP maksimal 2MB.',
            'pas_foto.required'            => 'Pas foto wajib diunggah.',
            'pas_foto.mimes'               => 'Pas foto harus berformat JPG atau PNG.',
            'pas_foto.max'                 => 'Ukuran pas foto maksimal 2MB.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validasi gagal. Periksa kembali data yang Anda masukkan.',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
