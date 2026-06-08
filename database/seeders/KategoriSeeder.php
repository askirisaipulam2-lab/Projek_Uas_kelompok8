<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Elektronik',
            'Dokumen',
            'Tas',
            'Dompet',
            'Pakaian',
            'Sepatu',
            'Aksesoris',
            'Kunci',
            'Kendaraan',
            'Lainnya',
        ];

        foreach ($data as $kategori) {
            Kategori::create([
                'nama' => $kategori,
                'deskripsi' => fake()->sentence(),
            ]);
        }
    }
}