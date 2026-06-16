<?php

namespace App\Enums;

/**
 * JHT claim status enumeration.
 *
 * Defines all possible claim states throughout the claim lifecycle.
 * Each status has human-readable label and UI color representation.
 */
enum KlaimStatus: string
{
    case PENDING = 'pending';
    case DIPROSES = 'diproses';
    case DISETUJUI = 'disetujui';
    case DITOLAK = 'ditolak';

    /**
     * Get human-readable label for this status.
     *
     * Used for UI display and user-facing messages.
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu Verifikasi',
            self::DIPROSES => 'Sedang Diproses',
            self::DISETUJUI => 'Disetujui',
            self::DITOLAK => 'Ditolak',
        };
    }

    /**
     * Get UI color representation for this status.
     *
     * Used for frontend status badges and visual indicators.
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::DIPROSES => 'blue',
            self::DISETUJUI => 'green',
            self::DITOLAK => 'red',
        };
    }
}
