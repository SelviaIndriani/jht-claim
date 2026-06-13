<?php

namespace Tests\Feature;

use App\Models\PesertaBpjs;
use App\Models\Layanan;
use Tests\TestCase;

class VerifyPesertaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create test layanan
        Layanan::factory()->create(['kode' => 'JHT', 'nama' => 'Jaminan Hari Tua']);

        // Create test peserta
        PesertaBpjs::factory()->create([
            'no_bpjs'          => '12345678901',
            'nik'              => '3201111234567890',
            'nama_lengkap'     => 'John Doe',
            'nama_ibu_kandung' => 'Jane Doe',
            'tempat_lahir'     => 'Jakarta',
            'tanggal_lahir'    => '1990-01-15',
            'email'            => 'john@example.com',
            'is_active'        => true,
            'layanan_ids'      => [1],
        ]);
    }

    public function test_verify_peserta_with_valid_data()
    {
        $response = $this->postJson('/api/peserta/verifikasi', [
            'no_bpjs' => '12345678901',
            'nik'     => '3201111234567890',
            'email'   => 'john@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data kepesertaan ditemukan.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'peserta' => [
                        'no_bpjs',
                        'nik',
                        'nama_lengkap',
                        'nama_ibu_kandung',
                        'tempat_lahir',
                        'tanggal_lahir',
                        'email',
                    ],
                    'layanan' => [
                        '*' => ['id', 'kode', 'nama', 'deskripsi'],
                    ],
                ],
            ]);

        $this->assertEquals('John Doe', $response->json('data.peserta.nama_lengkap'));
    }

    public function test_verify_peserta_with_invalid_nik_format()
    {
        $response = $this->postJson('/api/peserta/verifikasi', [
            'no_bpjs' => '12345678901',
            'nik'     => '123456789',  // Too short
            'email'   => 'john@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false])
            ->assertJsonPath('errors.nik', ['NIK harus tepat 16 digit.']);
    }

    public function test_verify_peserta_with_invalid_bpjs_format()
    {
        $response = $this->postJson('/api/peserta/verifikasi', [
            'no_bpjs' => 'ABC',  // Non-numeric
            'nik'     => '3201111234567890',
            'email'   => 'john@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false])
            ->assertJsonPath('errors.no_bpjs', ['Nomor KPJ harus tepat 11 digit.']);
    }

    public function test_verify_peserta_with_invalid_email_format()
    {
        $response = $this->postJson('/api/peserta/verifikasi', [
            'no_bpjs' => '12345678901',
            'nik'     => '3201111234567890',
            'email'   => 'invalid-email',
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false])
            ->assertJsonPath('errors.email');
    }

    public function test_verify_peserta_with_missing_fields()
    {
        $response = $this->postJson('/api/peserta/verifikasi', [
            'no_bpjs' => '12345678901',
            // missing nik and email
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false])
            ->assertJsonHasPath('errors.nik')
            ->assertJsonHasPath('errors.email');
    }

    public function test_verify_peserta_not_found()
    {
        $response = $this->postJson('/api/peserta/verifikasi', [
            'no_bpjs' => '99999999999',  // Non-existent
            'nik'     => '3201111234567890',
            'email'   => 'john@example.com',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Kombinasi Nomor KPJ, NIK, dan email tidak ditemukan. Pastikan data yang Anda masukkan sudah benar.',
            ]);
    }

    public function test_verify_peserta_with_mismatched_email()
    {
        $response = $this->postJson('/api/peserta/verifikasi', [
            'no_bpjs' => '12345678901',
            'nik'     => '3201111234567890',
            'email'   => 'wrong@example.com',  // Wrong email
        ]);

        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    }

    public function test_verify_inactive_peserta()
    {
        PesertaBpjs::where('no_bpjs', '12345678901')->update(['is_active' => false]);

        $response = $this->postJson('/api/peserta/verifikasi', [
            'no_bpjs' => '12345678901',
            'nik'     => '3201111234567890',
            'email'   => 'john@example.com',
        ]);

        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    }

    public function test_verify_peserta_email_case_insensitive()
    {
        $response = $this->postJson('/api/peserta/verifikasi', [
            'no_bpjs' => '12345678901',
            'nik'     => '3201111234567890',
            'email'   => 'JOHN@EXAMPLE.COM',  // Different case
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}
