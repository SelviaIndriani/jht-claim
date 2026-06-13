<?php

namespace Tests\Unit;

use App\Models\PesertaBpjs;
use App\Models\Layanan;
use App\Services\PesertaService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PesertaServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PesertaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PesertaService::class);

        // Create test data
        Layanan::factory()->create(['id' => 1, 'kode' => 'JHT', 'nama' => 'JHT', 'is_active' => true]);
        PesertaBpjs::factory()->create([
            'no_bpjs'          => '12345678901',
            'nik'              => '3201111234567890',
            'nama_lengkap'     => 'John Doe',
            'email'            => 'john@example.com',
            'is_active'        => true,
            'layanan_ids'      => [1],
        ]);
    }

    public function test_verifikasi_with_valid_data()
    {
        $result = $this->service->verifikasi('12345678901', '3201111234567890', 'john@example.com');

        $this->assertNotNull($result);
        $this->assertArrayHasKey('peserta', $result);
        $this->assertArrayHasKey('layanan', $result);
        $this->assertEquals('John Doe', $result['peserta']['nama_lengkap']);
    }

    public function test_verifikasi_with_invalid_bpjs()
    {
        $result = $this->service->verifikasi('99999999999', '3201111234567890', 'john@example.com');

        $this->assertNull($result);
    }

    public function test_verifikasi_with_invalid_nik()
    {
        $result = $this->service->verifikasi('12345678901', '9999999999999999', 'john@example.com');

        $this->assertNull($result);
    }

    public function test_verifikasi_with_invalid_email()
    {
        $result = $this->service->verifikasi('12345678901', '3201111234567890', 'wrong@example.com');

        $this->assertNull($result);
    }

    public function test_verifikasi_with_inactive_peserta()
    {
        PesertaBpjs::where('no_bpjs', '12345678901')->update(['is_active' => false]);

        $result = $this->service->verifikasi('12345678901', '3201111234567890', 'john@example.com');

        $this->assertNull($result);
    }

    public function test_verifikasi_email_case_insensitive()
    {
        $result = $this->service->verifikasi('12345678901', '3201111234567890', 'JOHN@EXAMPLE.COM');

        $this->assertNotNull($result);
        $this->assertEquals('john@example.com', $result['peserta']['email']);
    }

    public function test_verifikasi_returns_layanan()
    {
        $result = $this->service->verifikasi('12345678901', '3201111234567890', 'john@example.com');

        $this->assertNotEmpty($result['layanan']);
        $this->assertCount(1, $result['layanan']);
        $this->assertEquals('JHT', $result['layanan'][0]['kode']);
    }
}
