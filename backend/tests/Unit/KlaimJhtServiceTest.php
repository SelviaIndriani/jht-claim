<?php

namespace Tests\Unit;

use App\Models\PesertaBpjs;
use App\Models\Layanan;
use App\Models\KantorCabang;
use App\Services\KlaimJhtService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class KlaimJhtServiceTest extends TestCase
{
    use RefreshDatabase;

    protected KlaimJhtService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->service = app(KlaimJhtService::class);

        // Setup test data
        KantorCabang::factory()->create(['id' => 1, 'kode' => 'DKI', 'nama' => 'Jakarta']);
        Layanan::factory()->create(['id' => 1, 'kode' => 'JHT', 'nama' => 'JHT', 'is_active' => true]);
        PesertaBpjs::factory()->create([
            'no_bpjs'      => '12345678901',
            'nik'          => '3201111234567890',
            'is_active'    => true,
            'layanan_ids'  => [1],
        ]);
    }

    public function test_find_active_member_success()
    {
        $member = $this->service->findActiveMember('12345678901', '3201111234567890');

        $this->assertNotNull($member);
        $this->assertEquals('12345678901', $member->no_bpjs);
    }

    public function test_find_active_member_not_found()
    {
        $member = $this->service->findActiveMember('99999999999', '3201111234567890');

        $this->assertNull($member);
    }

    public function test_find_inactive_member_not_found()
    {
        PesertaBpjs::where('no_bpjs', '12345678901')->update(['is_active' => false]);

        $member = $this->service->findActiveMember('12345678901', '3201111234567890');

        $this->assertNull($member);
    }

    public function test_validate_member_fields_with_valid_data()
    {
        $member = PesertaBpjs::firstWhere('no_bpjs', '12345678901');
        $request = $this->createRequest([
            'nama_lengkap'     => $member->nama_lengkap,
            'nama_ibu_kandung' => $member->nama_ibu_kandung,
            'tempat_lahir'     => $member->tempat_lahir,
            'tanggal_lahir'    => $member->tanggal_lahir->format('Y-m-d'),
            'email'            => $member->email,
        ]);

        $errors = $this->service->validateMemberFields($request, $member);

        $this->assertEmpty($errors);
    }

    public function test_validate_member_fields_with_invalid_name()
    {
        $member = PesertaBpjs::firstWhere('no_bpjs', '12345678901');
        $request = $this->createRequest([
            'nama_lengkap'     => 'Wrong Name',
            'nama_ibu_kandung' => $member->nama_ibu_kandung,
            'tempat_lahir'     => $member->tempat_lahir,
            'tanggal_lahir'    => $member->tanggal_lahir->format('Y-m-d'),
            'email'            => $member->email,
        ]);

        $errors = $this->service->validateMemberFields($request, $member);

        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('nama_lengkap', $errors);
    }

    public function test_validate_layanan()
    {
        $member = PesertaBpjs::firstWhere('no_bpjs', '12345678901');

        $invalid = $this->service->validateLayanan([1], $member);
        $this->assertEmpty($invalid);

        $invalid = $this->service->validateLayanan([1, 99], $member);
        $this->assertContains(99, $invalid);
    }

    public function test_generate_no_klaim_format()
    {
        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('generateNoKlaim');
        $method->setAccessible(true);

        $noKlaim = $method->invoke($this->service);

        $this->assertMatchesRegularExpression('/^JHT-\d{8}-[A-Z0-9]{6}$/', $noKlaim);
    }

    protected function createRequest(array $data): Request
    {
        $request = app(Request::class);
        foreach ($data as $key => $value) {
            $request->request->set($key, $value);
        }
        return $request;
    }
}
