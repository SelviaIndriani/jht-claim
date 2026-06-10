<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PesertaBpjsSeeder extends Seeder
{
    public function run(): void
    {
        // layanan_ids references IDs from the layanan table (in seeder order):
        // 1=JHT, 2=JP, 3=JKK, 4=JKM, 5=JKP
        $members = [
            [
                'no_bpjs'          => '10000000001',
                'nik'              => '3171234501890001',
                'nama_lengkap'     => 'Budi Santoso',
                'nama_ibu_kandung' => 'Sri Wahyuni',
                'tempat_lahir'     => 'Jakarta',
                'tanggal_lahir'    => '1989-01-05',
                'email'            => 'budi.santoso@email.com',
                'layanan_ids'      => json_encode([1, 2, 3, 4, 5]),
                'is_active'        => true,
            ],
            [
                'no_bpjs'          => '10000000002',
                'nik'              => '3273015502900002',
                'nama_lengkap'     => 'Siti Rahayu',
                'nama_ibu_kandung' => 'Nur Hasanah',
                'tempat_lahir'     => 'Bandung',
                'tanggal_lahir'    => '1990-02-15',
                'email'            => 'siti.rahayu@email.com',
                'layanan_ids'      => json_encode([1, 3, 4]),
                'is_active'        => true,
            ],
            [
                'no_bpjs'          => '10000000003',
                'nik'              => '3578016703850003',
                'nama_lengkap'     => 'Ahmad Fauzi',
                'nama_ibu_kandung' => 'Siti Aminah',
                'tempat_lahir'     => 'Surabaya',
                'tanggal_lahir'    => '1985-03-27',
                'email'            => 'ahmad.fauzi@email.com',
                'layanan_ids'      => json_encode([1, 2, 3, 4]),
                'is_active'        => true,
            ],
            [
                'no_bpjs'          => '10000000004',
                'nik'              => '1271014504920004',
                'nama_lengkap'     => 'Dewi Lestari',
                'nama_ibu_kandung' => 'Ratna Dewi',
                'tempat_lahir'     => 'Medan',
                'tanggal_lahir'    => '1992-04-05',
                'email'            => 'dewi.lestari@email.com',
                'layanan_ids'      => json_encode([1, 5]),
                'is_active'        => true,
            ],
            [
                'no_bpjs'          => '10000000005',
                'nik'              => '3404016205880005',
                'nama_lengkap'     => 'Eko Prasetyo',
                'nama_ibu_kandung' => 'Endang Susilowati',
                'tempat_lahir'     => 'Yogyakarta',
                'tanggal_lahir'    => '1988-05-22',
                'email'            => 'eko.prasetyo@email.com',
                'layanan_ids'      => json_encode([1, 2, 4]),
                'is_active'        => true,
            ],
        ];

        DB::table('peserta_bpjs')->insert($members);
    }
}
