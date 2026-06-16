<?php

namespace App\Repositories;

use App\Models\PesertaBpjs;

/**
 * Peserta (participant) data repository.
 *
 * Provides data access layer for peserta-related queries.
 * Encapsulates database logic and ensures consistent query patterns.
 */
class PesertaRepository
{
    /**
     * Find active peserta by BPJS number and NIK combination.
     *
     * @param string $noBpjs BPJS number (11 digits)
     * @param string $nik National ID number (16 digits)
     * @return PesertaBpjs|null Active peserta or null if not found
     */
    public function findActiveByCombination(string $noBpjs, string $nik): ?PesertaBpjs
    {
        return PesertaBpjs::where('no_bpjs', $noBpjs)
            ->where('nik', $nik)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Find active peserta by ID.
     *
     * @param int $id Peserta primary key
     * @return PesertaBpjs|null Active peserta or null if not found or inactive
     */
    public function findActiveById(int $id): ?PesertaBpjs
    {
        return PesertaBpjs::where('id', $id)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Find active peserta by BPJS number.
     *
     * @param string $noBpjs BPJS number (11 digits)
     * @return PesertaBpjs|null Active peserta or null if not found
     */
    public function findByNoBpjs(string $noBpjs): ?PesertaBpjs
    {
        return PesertaBpjs::where('no_bpjs', $noBpjs)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Load peserta with related layanan (services).
     *
     * @param PesertaBpjs $peserta Peserta model instance
     * @return PesertaBpjs Peserta with layanan relationship loaded
     */
    public function getWithLayanan(PesertaBpjs $peserta): PesertaBpjs
    {
        return $peserta->load('layanan');
    }
}
