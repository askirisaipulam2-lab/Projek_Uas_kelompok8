<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            KategoriSeeder::class,
            LokasiSeeder::class,
            TagSeeder::class,
            LaporanKehilanganSeeder::class,
            LaporanTemuanSeeder::class,
            KlaimSeeder::class,
            NotifikasiSeeder::class,
        ]);
    }
}
