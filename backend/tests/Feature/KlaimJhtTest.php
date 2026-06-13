<?php

namespace Tests\Feature;

use App\Models\PesertaBpjs;
use App\Models\KlaimJht;
use App\Models\Layanan;
use App\Models\KantorCabang;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class KlaimJhtTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        // Create test data
        KantorCabang::factory()->create([
            'id'        => 1,
            'kode'      => 'DKI',
            'nama'      => 'Jakarta Pusat',
            'alamat'    => 'Jl. Sudirman',
            'provinsi'  => 'DKI Jakarta',
            'kota'      => 'Jakarta',
            'telepon'   => '021123456',
        ]);

        Layanan::factory()->create([
            'id'        => 1,
            'kode'      => 'JHT',
            'nama'      => 'Jaminan Hari Tua',
            'is_active' => true,
        ]);

        PesertaBpjs::factory()->create([
            'no_bpjs'          => '12345678901',
            'nik'              => '3201111234567890',
            'nama_lengkap'     => 'John Doe',
            'nama_ibu_kandung' => 'Jane Doe',
            'tempat_lahir'     => 'Jakarta',
            'tanggal_lahir'    => '1990-01-15',
            'email'            => 'john@example.com',
            'kantor_id'        => 1,
            'is_active'        => true,
            'layanan_ids'      => [1],
        ]);
    }

    public function test_submit_klaim_with_valid_data()
    {
        $response = $this->postJson('/api/klaim', [
            'no_bpjs'          => '12345678901',
            'nik'              => '3201111234567890',
            'nama_lengkap'     => 'John Doe',
            'nama_ibu_kandung' => 'Jane Doe',
            'tempat_lahir'     => 'Jakarta',
            'tanggal_lahir'    => '1990-01-15',
            'email'            => 'john@example.com',
            'sebab_klaim'      => 'mengundurkan_diri',
            'layanan_dipilih'  => [1],
            'cara_konfirmasi'  => 'video_call',
            'foto_ktp'         => UploadedFile::fake()->image('ktp.jpg', 100, 100),
            'pas_foto'         => UploadedFile::fake()->image('pasfoto.jpg', 100, 100),
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'no_klaim',
                    'status',
                    'submitted_at',
                    'email',
                ],
            ]);

        $this->assertDatabaseHas('klaim_jht', [
            'no_bpjs'     => '12345678901',
            'nik'         => '3201111234567890',
            'sebab_klaim' => 'mengundurkan_diri',
            'status'      => 'pending',
        ]);
    }

    public function test_submit_klaim_with_invalid_data()
    {
        $response = $this->postJson('/api/klaim', [
            'no_bpjs'      => 'invalid',
            'nik'          => 'invalid',
            'nama_lengkap' => 'John',  // Too short
            // Missing required fields
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_submit_klaim_with_non_existent_peserta()
    {
        $response = $this->postJson('/api/klaim', [
            'no_bpjs'          => '99999999999',  // Non-existent
            'nik'              => '3201111234567890',
            'nama_lengkap'     => 'John Doe',
            'nama_ibu_kandung' => 'Jane Doe',
            'tempat_lahir'     => 'Jakarta',
            'tanggal_lahir'    => '1990-01-15',
            'email'            => 'john@example.com',
            'sebab_klaim'      => 'mengundurkan_diri',
            'layanan_dipilih'  => [1],
            'cara_konfirmasi'  => 'video_call',
            'foto_ktp'         => UploadedFile::fake()->image('ktp.jpg', 100, 100),
            'pas_foto'         => UploadedFile::fake()->image('pasfoto.jpg', 100, 100),
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_submit_klaim_with_mismatched_peserta_data()
    {
        $response = $this->postJson('/api/klaim', [
            'no_bpjs'          => '12345678901',
            'nik'              => '3201111234567890',
            'nama_lengkap'     => 'Wrong Name',  // Doesn't match peserta
            'nama_ibu_kandung' => 'Jane Doe',
            'tempat_lahir'     => 'Jakarta',
            'tanggal_lahir'    => '1990-01-15',
            'email'            => 'john@example.com',
            'sebab_klaim'      => 'mengundurkan_diri',
            'layanan_dipilih'  => [1],
            'cara_konfirmasi'  => 'video_call',
            'foto_ktp'         => UploadedFile::fake()->image('ktp.jpg', 100, 100),
            'pas_foto'         => UploadedFile::fake()->image('pasfoto.jpg', 100, 100),
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_submit_klaim_with_invalid_file_size()
    {
        $response = $this->postJson('/api/klaim', [
            'no_bpjs'          => '12345678901',
            'nik'              => '3201111234567890',
            'nama_lengkap'     => 'John Doe',
            'nama_ibu_kandung' => 'Jane Doe',
            'tempat_lahir'     => 'Jakarta',
            'tanggal_lahir'    => '1990-01-15',
            'email'            => 'john@example.com',
            'sebab_klaim'      => 'mengundurkan_diri',
            'layanan_dipilih'  => [1],
            'cara_konfirmasi'  => 'video_call',
            'foto_ktp'         => UploadedFile::fake()->image('ktp.jpg')->size(3000),  // >2MB
            'pas_foto'         => UploadedFile::fake()->image('pasfoto.jpg', 100, 100),
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_get_klaim_status()
    {
        $klaim = KlaimJht::factory()->create([
            'no_bpjs'     => '12345678901',
            'nik'         => '3201111234567890',
            'sebab_klaim' => 'mengundurkan_diri',
            'status'      => 'pending',
        ]);

        $response = $this->getJson("/api/klaim/{$klaim->no_klaim}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'no_klaim'     => $klaim->no_klaim,
                    'sebab_klaim'  => 'mengundurkan_diri',
                    'status'       => 'pending',
                ],
            ]);
    }

    public function test_get_klaim_not_found()
    {
        $response = $this->getJson('/api/klaim/INVALID-NUMBER');

        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    }

    public function test_klaim_uses_soft_delete()
    {
        $klaim = KlaimJht::factory()->create();

        $klaim->delete();

        $this->assertSoftDeleted('klaim_jht', ['id' => $klaim->id]);
        $this->assertNull(KlaimJht::find($klaim->id));
        $this->assertNotNull(KlaimJht::withTrashed()->find($klaim->id));
    }
}
