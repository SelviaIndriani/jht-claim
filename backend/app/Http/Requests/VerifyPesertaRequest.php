<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyPesertaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'no_bpjs' => ['required', 'string', 'regex:/^\d{11}$/'],
            'nik'     => ['required', 'string', 'regex:/^\d{16}$/'],
            'email'   => ['required', 'email:rfc,dns'],
        ];
    }

    public function messages(): array
    {
        return [
            'no_bpjs.required' => 'Nomor KPJ wajib diisi.',
            'no_bpjs.regex'    => 'Nomor KPJ harus tepat 11 digit.',
            'nik.required'     => 'NIK wajib diisi.',
            'nik.regex'        => 'NIK harus tepat 16 digit.',
            'email.required'   => 'Alamat email wajib diisi.',
            'email.email'      => 'Format alamat email tidak valid.',
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
