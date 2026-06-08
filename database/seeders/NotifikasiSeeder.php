<?php

namespace Database\Seeders;

use App\Models\Notifikasi;
use Illuminate\Database\Seeder;

class NotifikasiSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {

            Notifikasi::create([
                'user_id' => rand(1, 21),
                'judul' => fake()->randomElement([
                    'Laporan Kehilangan',
                    'Laporan Temuan',
                    'Klaim Barang',
                ]),
                'pesan' => fake()->sentence(),
                'is_read' => fake()->boolean(),
            ]);
        }
    }
}