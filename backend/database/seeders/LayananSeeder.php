<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LayananSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kode'        => 'JHT',
                'nama'        => 'Jaminan Hari Tua (JHT)',
                'deskripsi'   => 'Manfaat uang tunai yang dibayarkan sekaligus pada saat peserta memasuki usia pensiun, meninggal dunia, atau mengalami cacat total tetap.',
                'is_active'   => true,
            ],
            [
                'kode'        => 'JP',
                'nama'        => 'Jaminan Pensiun (JP)',
                'deskripsi'   => 'Manfaat uang tunai bulanan yang diterima peserta pada saat memasuki usia pensiun, mengalami cacat total tetap, atau ahli waris peserta yang meninggal dunia.',
                'is_active'   => true,
            ],
            [
                'kode'        => 'JKK',
                'nama'        => 'Jaminan Kecelakaan Kerja (JKK)',
                'deskripsi'   => 'Memberikan perlindungan atas risiko kecelakaan yang terjadi dalam hubungan kerja, termasuk kecelakaan yang terjadi dalam perjalanan dari rumah menuju tempat kerja atau sebaliknya.',
                'is_active'   => true,
            ],
            [
                'kode'        => 'JKM',
                'nama'        => 'Jaminan Kematian (JKM)',
                'deskripsi'   => 'Memberikan manfaat uang tunai kepada ahli waris ketika peserta meninggal dunia bukan akibat kecelakaan kerja.',
                'is_active'   => true,
            ],
            [
                'kode'        => 'JKP',
                'nama'        => 'Jaminan Kehilangan Pekerjaan (JKP)',
                'deskripsi'   => 'Memberikan manfaat berupa uang tunai, akses informasi pasar kerja, dan pelatihan kerja bagi peserta yang mengalami PHK.',
                'is_active'   => true,
            ],
        ];

        DB::table('layanan')->insert($data);
    }
}
