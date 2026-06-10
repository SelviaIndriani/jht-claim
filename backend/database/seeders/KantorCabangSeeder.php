<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KantorCabangSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kode'      => 'KC-JKT-01',
                'nama'      => 'Kantor Cabang Jakarta Pusat',
                'alamat'    => 'Jl. Jend. Gatot Subroto No.79, RT.6/RW.1, Karet Semanggi',
                'kota'      => 'Jakarta Pusat',
                'provinsi'  => 'DKI Jakarta',
                'telepon'   => '021-52900600',
                'email'     => 'kc.jakartapusat@bpjsketenagakerjaan.go.id',
                'is_active' => true,
            ],
            [
                'kode'      => 'KC-JKT-02',
                'nama'      => 'Kantor Cabang Jakarta Selatan',
                'alamat'    => 'Jl. Raya Pasar Minggu No.17, Pancoran',
                'kota'      => 'Jakarta Selatan',
                'provinsi'  => 'DKI Jakarta',
                'telepon'   => '021-79190907',
                'email'     => 'kc.jakartaselatan@bpjsketenagakerjaan.go.id',
                'is_active' => true,
            ],
            [
                'kode'      => 'KC-JKT-03',
                'nama'      => 'Kantor Cabang Jakarta Utara',
                'alamat'    => 'Jl. Pluit Raya No.1, Penjaringan',
                'kota'      => 'Jakarta Utara',
                'provinsi'  => 'DKI Jakarta',
                'telepon'   => '021-6606945',
                'email'     => 'kc.jakartautara@bpjsketenagakerjaan.go.id',
                'is_active' => true,
            ],
            [
                'kode'      => 'KC-BDG-01',
                'nama'      => 'Kantor Cabang Bandung Kota',
                'alamat'    => 'Jl. LL RE Martadinata No.33',
                'kota'      => 'Bandung',
                'provinsi'  => 'Jawa Barat',
                'telepon'   => '022-4203525',
                'email'     => 'kc.bandungkota@bpjsketenagakerjaan.go.id',
                'is_active' => true,
            ],
            [
                'kode'      => 'KC-SBY-01',
                'nama'      => 'Kantor Cabang Surabaya Darmo',
                'alamat'    => 'Jl. Raya Darmo No.105-107',
                'kota'      => 'Surabaya',
                'provinsi'  => 'Jawa Timur',
                'telepon'   => '031-5613804',
                'email'     => 'kc.surabayadarmo@bpjsketenagakerjaan.go.id',
                'is_active' => true,
            ],
            [
                'kode'      => 'KC-MDN-01',
                'nama'      => 'Kantor Cabang Medan Kota',
                'alamat'    => 'Jl. Jend. Katamso No.1, Sei Rengas II',
                'kota'      => 'Medan',
                'provinsi'  => 'Sumatera Utara',
                'telepon'   => '061-4158185',
                'email'     => 'kc.medankota@bpjsketenagakerjaan.go.id',
                'is_active' => true,
            ],
            [
                'kode'      => 'KC-YGY-01',
                'nama'      => 'Kantor Cabang Yogyakarta',
                'alamat'    => 'Jl. Urip Sumoharjo No.106',
                'kota'      => 'Yogyakarta',
                'provinsi'  => 'DI Yogyakarta',
                'telepon'   => '0274-588435',
                'email'     => 'kc.yogyakarta@bpjsketenagakerjaan.go.id',
                'is_active' => true,
            ],
            [
                'kode'      => 'KC-SMG-01',
                'nama'      => 'Kantor Cabang Semarang Pemuda',
                'alamat'    => 'Jl. Pemuda No.72',
                'kota'      => 'Semarang',
                'provinsi'  => 'Jawa Tengah',
                'telepon'   => '024-3566080',
                'email'     => 'kc.semarangpemuda@bpjsketenagakerjaan.go.id',
                'is_active' => true,
            ],
        ];

        DB::table('kantor_cabang')->insert($data);
    }
}
