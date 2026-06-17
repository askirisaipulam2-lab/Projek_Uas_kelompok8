<?php

namespace Database\Seeders;

use App\Models\LaporanKehilangan;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class LaporanKehilanganSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $laporan = LaporanKehilangan::create([
                'user_id' => rand(2, 21),
                'kategori_id' => rand(1, 10),
                'lokasi_id' => rand(1, 20),
                'judul' => fake()->sentence(3),
                'deskripsi' => fake()->paragraph(),
                'tanggal_hilang' => fake()->date(),
                'status' => fake()->randomElement([
                    'hilang',
                    'ditemukan',
                    'diklaim',
                ]),
            ]);

            $laporan->tags()->attach(
                Tag::inRandomOrder()
                    ->limit(rand(1, 3))
                    ->pluck('id')
            );
        }
    }
}
