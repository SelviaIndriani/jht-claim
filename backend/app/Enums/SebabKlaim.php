<?php

namespace App\Enums;

/**
 * Claim reason enumeration for JHT claims.
 *
 * Defines the valid reasons a peserta (participant) can claim JHT benefits.
 * BPJS regulation allows claims only for termination scenarios.
 */
enum SebabKlaim: string
{
    case MENGUNDURKAN_DIRI = 'mengundurkan_diri';
    case BERAKHIR_KONTRAK = 'berakhir_kontrak';

    /**
     * Get display label for claim reason.
     */
    public function label(): string
    {
        return match($this) {
            self::MENGUNDURKAN_DIRI => 'Mengundurkan Diri',
            self::BERAKHIR_KONTRAK => 'Habis Masa Kontrak',
        };
    }

    /**
     * Get detailed description of claim reason.
     */
    public function description(): string
    {
        return match($this) {
            self::MENGUNDURKAN_DIRI => 'Peserta mengundurkan diri dari pekerjaan',
            self::BERAKHIR_KONTRAK => 'Kontrak kerja peserta telah berakhir',
        };
    }
}
