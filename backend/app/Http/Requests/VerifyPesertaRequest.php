<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Form request validation for peserta/member verification.
 *
 * Validates BPJS number, NIK, and email combination for claim eligibility.
 * Throws HTTP 422 on validation failure with structured error response.
 */
class VerifyPesertaRequest extends FormRequest
{
    /**
     * Authorization check - all requests are permitted.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define validation rules for peserta verification.
     *
     * Rules:
     * - no_bpjs: Required, 11-digit string (BPJS number format)
     * - nik: Required, 16-digit string (Indonesian National ID format)
     * - email: Required, valid RFC compliant email with DNS verification
     */
    public function rules(): array
    {
        return [
            'no_bpjs' => ['required', 'string', 'regex:/^\d{11}$/'],
            'nik'     => ['required', 'string', 'regex:/^\d{16}$/'],
            'email'   => ['required', 'email:rfc,dns'],
        ];
    }

    /**
     * Define custom validation error messages.
     *
     * Messages are localized for Indonesian users.
     */
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

    /**
     * Handle validation failure with structured JSON response.
     *
     * Returns HTTP 422 with validation errors in expected format.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
