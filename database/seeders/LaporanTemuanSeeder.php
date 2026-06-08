<?php

namespace Database\Seeders;

use App\Models\LaporanTemuan;
use Illuminate\Database\Seeder;

class LaporanTemuanSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {

            LaporanTemuan::create([
                'user_id' => rand(2, 21),
                'kategori_id' => rand(1, 10),
                'lokasi_id' => rand(1, 20),
                'judul' => fake()->sentence(3),
                'deskripsi' => fake()->paragraph(),
                'tanggal_temuan' => fake()->date(),
                'status' => fake()->randomElement([
                    'ditemukan',
                    'diklaim'
                ]),
            ]);
        }
    }
}