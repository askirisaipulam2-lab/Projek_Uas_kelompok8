<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Laptop',
            'Asus',
            'Acer',
            'Hitam',
            'Putih',
            'Dompet',
            'Tas',
            'Honda',
            'Yamaha',
            'Mahasiswa',
            'Elektronik',
            'Kunci',
            'Sepatu',
            'Jaket',
            'Kacamata',
            'Smartphone',
            'Samsung',
            'iPhone',
            'Dokumen',
            'KTM',
        ];

        foreach ($tags as $tag) {
            Tag::create([
                'nama' => $tag,
            ]);
        }
    }
}