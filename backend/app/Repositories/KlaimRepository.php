<?php

namespace App\Repositories;

use App\Models\KlaimJht;
use App\Enums\KlaimStatus;
use Illuminate\Database\Eloquent\Collection;

/**
 * Klaim JHT (Old Age Insurance Claim) data repository.
 *
 * Provides data access layer for claim-related queries.
 * Centralizes database logic for claim management operations.
 */
class KlaimRepository
{
    /**
     * Create a new claim record.
     *
     * @param array $data Claim attributes
     * @return KlaimJht Created claim model
     */
    public function create(array $data): KlaimJht
    {
        return KlaimJht::create($data);
    }

    /**
     * Find claim by unique claim number.
     *
     * @param string $noKlaim Unique claim number (e.g., JHT-20260613-ABC123)
     * @return KlaimJht|null Claim or null if not found
     */
    public function findByNoKlaim(string $noKlaim): ?KlaimJht
    {
        return KlaimJht::where('no_klaim', $noKlaim)->first();
    }

    /**
     * Find claim by primary key ID.
     *
     * @param int $id Claim primary key
     * @return KlaimJht|null Claim or null if not found
     */
    public function findById(int $id): ?KlaimJht
    {
        return KlaimJht::find($id);
    }

    /**
     * Find most recent claim for peserta by BPJS and NIK.
     *
     * @param string $noBpjs BPJS number
     * @param string $nik National ID number
     * @return KlaimJht|null Most recent claim or null if not found
     */
    public function findByNoBpjsAndNik(string $noBpjs, string $nik): ?KlaimJht
    {
        return KlaimJht::where('no_bpjs', $noBpjs)
            ->where('nik', $nik)
            ->latest('created_at')
            ->first();
    }

    /**
     * Get claims by status with limit.
     *
     * @param KlaimStatus $status Claim status filter
     * @param int $limit Maximum number of records to return
     * @return Collection Claims matching status, ordered by newest first
     */
    public function getByStatus(KlaimStatus $status, int $limit = 50): Collection
    {
        return KlaimJht::where('status', $status)
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Update claim status with optional notes.
     *
     * @param KlaimJht $klaim Claim model instance
     * @param KlaimStatus $status New status value
     * @param string|null $notes Optional status change notes
     * @return bool True if update successful, false otherwise
     */
    public function updateStatus(KlaimJht $klaim, KlaimStatus $status, ?string $notes = null): bool
    {
        $klaim->status = $status;
        if ($notes) {
            $klaim->notes = $notes;
        }
        return $klaim->save();
    }
}
