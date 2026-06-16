<?php

namespace App\Utilities;

/**
 * Data masking utility for protecting sensitive information in logs.
 *
 * Automatically masks personally identifiable information (PII) and
 * financial data to comply with GDPR and data protection regulations.
 * Supports recursive masking of nested arrays and objects.
 */
class DataMasking
{
    /**
     * List of field names that should be automatically masked.
     * Case-insensitive substring matching.
     */
    protected static array $sensitiveFields = [
        'password', 'token', 'api_key', 'secret', 'private_key',
        'email', 'phone', 'mobile', 'nik', 'no_bpjs', 'no_rekening',
        'credit_card', 'cvv', 'pin', 'otp',
        'nama_ibu_kandung', 'tempat_lahir',
        'authorization', 'x-api-key', 'cookie',
    ];

    /**
     * Mask sensitive data dalam array/object
     */
    public static function mask(array $data): array
    {
        $masked = [];

        foreach ($data as $key => $value) {
            if (self::isSensitiveField($key)) {
                $masked[$key] = self::maskValue($value, $key);
            } elseif (is_array($value)) {
                $masked[$key] = self::mask($value);
            } elseif (is_object($value)) {
                $masked[$key] = self::mask((array) $value);
            } else {
                $masked[$key] = $value;
            }
        }

        return $masked;
    }

    /**
     * Mask single value based on field type
     */
    public static function maskValue($value, string $fieldName = ''): string
    {
        if (is_null($value) || $value === '') {
            return '';
        }

        $value = (string) $value;

        // Email: user***@example.com
        if (self::isEmail($value)) {
            return self::maskEmail($value);
        }

        // NIK: 32011112****7890
        if ($fieldName === 'nik' || self::isNik($value)) {
            return self::maskNik($value);
        }

        // BPJS: 1234****123
        if ($fieldName === 'no_bpjs' || self::isBpjs($value)) {
            return self::maskBpjs($value);
        }

        // Phone: +62812****5678
        if (self::isPhone($value)) {
            return self::maskPhone($value);
        }

        // Credit Card: 4111****1111
        if (self::isCreditCard($value)) {
            return self::maskCreditCard($value);
        }

        // Generic: show first 2 chars, rest masked
        return self::maskGeneric($value);
    }

    /**
     * Check if field name is sensitive
     */
    protected static function isSensitiveField(string $fieldName): bool
    {
        $fieldLower = strtolower($fieldName);

        foreach (self::$sensitiveFields as $sensitive) {
            if (strpos($fieldLower, $sensitive) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Mask email: user***@example.com
     */
    protected static function maskEmail(string $email): string
    {
        if (!str_contains($email, '@')) {
            return '***@***';
        }

        [$user, $domain] = explode('@', $email);

        if (strlen($user) <= 2) {
            $maskedUser = str_repeat('*', strlen($user));
        } else {
            $maskedUser = substr($user, 0, 2) . str_repeat('*', strlen($user) - 2);
        }

        return "{$maskedUser}@{$domain}";
    }

    /**
     * Mask NIK: 32011112****7890
     */
    protected static function maskNik(string $nik): string
    {
        if (strlen($nik) < 8) {
            return str_repeat('*', strlen($nik));
        }

        return substr($nik, 0, 8) . str_repeat('*', strlen($nik) - 12) . substr($nik, -4);
    }

    /**
     * Mask BPJS: 1234****123
     */
    protected static function maskBpjs(string $bpjs): string
    {
        if (strlen($bpjs) < 8) {
            return str_repeat('*', strlen($bpjs));
        }

        return substr($bpjs, 0, 4) . str_repeat('*', strlen($bpjs) - 7) . substr($bpjs, -3);
    }

    /**
     * Mask phone: +62812****5678
     */
    protected static function maskPhone(string $phone): string
    {
        if (strlen($phone) < 6) {
            return str_repeat('*', strlen($phone));
        }

        return substr($phone, 0, 5) . str_repeat('*', strlen($phone) - 9) . substr($phone, -4);
    }

    /**
     * Mask credit card: 4111****1111
     */
    protected static function maskCreditCard(string $card): string
    {
        $card = preg_replace('/\D/', '', $card);

        if (strlen($card) < 8) {
            return str_repeat('*', strlen($card));
        }

        return substr($card, 0, 4) . str_repeat('*', strlen($card) - 8) . substr($card, -4);
    }

    /**
     * Generic masking: **user**
     */
    protected static function maskGeneric(string $value): string
    {
        if (strlen($value) <= 2) {
            return str_repeat('*', strlen($value));
        }

        return str_repeat('*', strlen($value) - 2) . substr($value, -2);
    }

    /**
     * Validation checks
     */
    protected static function isEmail(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected static function isNik(string $value): bool
    {
        return preg_match('/^\d{16}$/', $value);
    }

    protected static function isBpjs(string $value): bool
    {
        return preg_match('/^\d{11}$/', $value);
    }

    protected static function isPhone(string $value): bool
    {
        return preg_match('/^(\+62|0)[0-9]{9,12}$/', $value);
    }

    protected static function isCreditCard(string $value): bool
    {
        $value = preg_replace('/\D/', '', $value);
        return strlen($value) >= 13 && strlen($value) <= 19;
    }
}
