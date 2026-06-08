<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\LaporanKehilangan;

for ($i = 1; $i <= 20; $i++) {

    $laporan = LaporanKehilangan::create([
        'user_id' => rand(2, 21),
        'kategori_id' => rand(1, 10),
        'lokasi_id' => rand(1, 20),
        'judul' => fake()->sentence(3),
        'deskripsi' => fake()->paragraph(),
        'tanggal_hilang' => fake()->date(),
        'status' => 'hilang',
    ]);

    $laporan->tags()->attach(
        Tag::inRandomOrder()
            ->limit(rand(1, 3))
            ->pluck('id')
    );
}