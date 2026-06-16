<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when a peserta (participant) cannot be found or is inactive.
 *
 * Used during claim verification when BPJS number and NIK combination
 * does not exist in the system or the peserta is no longer active.
 */
class PesertaNotFoundException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param string $noBpjs BPJS number (will be masked in response)
     * @param string $nik National ID number (will be masked in response)
     */
    public function __construct(string $noBpjs = '', string $nik = '')
    {
        parent::__construct(
            "Peserta dengan Nomor KPJ {$noBpjs} dan NIK {$nik} tidak ditemukan atau tidak aktif."
        );
    }

    /**
     * Render exception as JSON response.
     *
     * Returns HTTP 404 with user-friendly message.
     */
    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
        ], 404);
    }
}
