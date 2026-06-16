<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * Audit log model for tracking all system changes.
 *
 * Records before/after snapshots, user actions, timestamps, and IP addresses
 * for compliance and debugging purposes.
 *
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $model
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog byAction($action)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog byUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog dateRange($startDate, $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog forModel($modelClass)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAuditLog {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $kode
 * @property string|null $nama
 * @property string|null $alamat
 * @property string|null $kota
 * @property string|null $provinsi
 * @property string|null $telepon
 * @property string|null $email
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\KlaimJht> $klaimJht
 * @property-read int|null $klaim_jht_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantorCabang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantorCabang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantorCabang query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantorCabang whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantorCabang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantorCabang whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantorCabang whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantorCabang whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantorCabang whereKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantorCabang whereKota($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantorCabang whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantorCabang whereProvinsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantorCabang whereTelepon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KantorCabang whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperKantorCabang {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $no_klaim
 * @property string|null $no_bpjs
 * @property string|null $nik
 * @property string|null $nama_lengkap
 * @property string|null $nama_ibu_kandung
 * @property string|null $tempat_lahir
 * @property \Illuminate\Support\Carbon|null $tanggal_lahir
 * @property string|null $email
 * @property \App\Enums\SebabKlaim|null $sebab_klaim
 * @property array<array-key, mixed>|null $layanan_dipilih
 * @property \App\Enums\CaraKonfirmasi|null $cara_konfirmasi
 * @property string|null $foto_ktp
 * @property string|null $pas_foto
 * @property \App\Enums\KlaimStatus $status
 * @property int|null $kantor_cabang_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \App\Models\KantorCabang|null $kantorCabang
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht whereCaraKonfirmasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht whereFotoKtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht whereKantorCabangId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht whereLayananDipilih($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht whereNamaIbuKandung($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht whereNamaLengkap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht whereNik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht whereNoBpjs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht whereNoKlaim($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht wherePasFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht whereSebabKlaim($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht whereTanggalLahir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht whereTempatLahir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KlaimJht withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperKlaimJht {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $kode
 * @property string|null $nama
 * @property string|null $deskripsi
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Layanan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Layanan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Layanan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Layanan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Layanan whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Layanan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Layanan whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Layanan whereKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Layanan whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Layanan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLayanan {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $no_bpjs
 * @property string $nik
 * @property string $nama_lengkap
 * @property string|null $nama_ibu_kandung
 * @property string $tempat_lahir
 * @property \Illuminate\Support\Carbon $tanggal_lahir
 * @property string|null $email
 * @property array<array-key, mixed>|null $layanan_ids
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesertaBpjs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesertaBpjs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesertaBpjs onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesertaBpjs query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesertaBpjs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesertaBpjs whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesertaBpjs whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesertaBpjs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesertaBpjs whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesertaBpjs whereLayananIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesertaBpjs whereNamaIbuKandung($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesertaBpjs whereNamaLengkap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesertaBpjs whereNik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesertaBpjs whereNoBpjs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesertaBpjs whereTanggalLahir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesertaBpjs whereTempatLahir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesertaBpjs whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesertaBpjs withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesertaBpjs withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPesertaBpjs {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

