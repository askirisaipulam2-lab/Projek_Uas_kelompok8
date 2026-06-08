<?php

namespace Database\Seeders;

use App\Models\Klaim;
use Illuminate\Database\Seeder;

class KlaimSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {

            Klaim::create([
                'user_id' => rand(2, 21),

                'laporan_temuan_id' => rand(1, 20),

                'bukti_kepemilikan' => fake()->paragraph(),

                'status' => fake()->randomElement([
                    'menunggu',
                    'disetujui',
                    'ditolak',
                ]),
            ]);
        }
    }
}