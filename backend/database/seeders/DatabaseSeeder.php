<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            KantorCabangSeeder::class,
            LayananSeeder::class,
            PesertaBpjsSeeder::class,
        ]);
    }
}
