<?php

namespace App\Enums;

/**
 * Verification confirmation method enumeration.
 *
 * Defines available methods for peserta to confirm their identity
 * during the claim verification process.
 */
enum CaraKonfirmasi: string
{
    case VIDEO_CALL = 'video_call';
    case DATANG_KANTOR = 'datang_kantor';

    /**
     * Get display label for confirmation method.
     */
    public function label(): string
    {
        return match($this) {
            self::VIDEO_CALL => 'Video Call',
            self::DATANG_KANTOR => 'Datang ke Kantor',
        };
    }

    /**
     * Get detailed description of confirmation method.
     */
    public function description(): string
    {
        return match($this) {
            self::VIDEO_CALL => 'Verifikasi melalui video call',
            self::DATANG_KANTOR => 'Verifikasi datang langsung ke kantor cabang',
        };
    }
}
