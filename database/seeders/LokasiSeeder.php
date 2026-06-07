<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lokasi;

class LokasiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Gedung A',
            'Gedung B',
            'Gedung C',
            'Perpustakaan',
            'Laboratorium Komputer',
            'Laboratorium Bahasa',
            'Masjid Kampus',
            'Kantin Utama',
            'Kantin Timur',
            'Parkiran Motor',
            'Parkiran Mobil',
            'Lapangan',
            'Aula Kampus',
            'Ruang Dosen',
            'Ruang Akademik',
            'Ruang Kemahasiswaan',
            'Toilet Gedung A',
            'Toilet Gedung B',
            'Pos Satpam',
            'Taman Kampus',
        ];

        foreach ($data as $lokasi) {
            Lokasi::create([
                'nama' => $lokasi,
                'deskripsi' => 'Lokasi ' . $lokasi,
            ]);
        }
    }
}
