<?php

namespace App\Traits;

/**
 * Input validation trait for common validation patterns.
 *
 * Provides reusable validation methods for Indonesian identity documents
 * and personal information. Methods are used across services and controllers
 * to ensure consistent validation logic.
 */
trait ValidatesInput
{
    /**
     * Validate Indonesian National ID (NIK) format.
     *
     * NIK must be exactly 16 digits with first digit not zero.
     *
     * @param string $nik National ID number to validate
     * @return bool True if valid NIK format, false otherwise
     */
    protected function validateNik(string $nik): bool
    {
        return preg_match('/^\d{16}$/', $nik) && preg_match('/^[1-9]\d{15}$/', $nik);
    }

    protected function validateNoBpjs(string $noBpjs): bool
    {
        return preg_match('/^\d{11}$/', $noBpjs);
    }

    protected function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function normalizeString(string $value): string
    {
        return strtolower(trim($value));
    }

    protected function stringMatch(string $value1, string $value2): bool
    {
        return $this->normalizeString($value1) === $this->normalizeString($value2);
    }
}
