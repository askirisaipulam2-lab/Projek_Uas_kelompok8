<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Elektronik',
            'Berharga',
            'Dokumen',
            'Aksesoris',
            'Pribadi',
            'Penting',
            'Kampus',
            'Mahasiswa',
            'Dosen',
            'Karyawan',
            'Hitam',
            'Putih',
            'Merah',
            'Biru',
            'Kecil',
            'Besar',
            'Baru',
            'Bekas',
            'Mahal',
            'Murah',
        ];

        foreach ($tags as $tag) {
            Tag::create([
                'nama' => $tag,
            ]);
        }
    }
}
