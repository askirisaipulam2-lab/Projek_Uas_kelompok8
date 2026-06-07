<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama' => 'Laptop'],
            ['nama' => 'Handphone'],
            ['nama' => 'Tas'],
            ['nama' => 'Dompet'],
            ['nama' => 'Kunci'],
            ['nama' => 'Jam Tangan'],
            ['nama' => 'Helm'],
            ['nama' => 'Power Bank'],
            ['nama' => 'Flashdisk'],
            ['nama' => 'Mouse'],
            ['nama' => 'Keyboard'],
            ['nama' => 'Buku'],
            ['nama' => 'Kacamata'],
            ['nama' => 'Jaket'],
            ['nama' => 'Sepatu'],
            ['nama' => 'Kartu Mahasiswa'],
            ['nama' => 'Charger'],
            ['nama' => 'Headset'],
            ['nama' => 'Tablet'],
            ['nama' => 'Dokumen'],
        ];

        foreach ($data as $item) {
            Kategori::create([
                'nama' => $item['nama'],
                'deskripsi' => 'Kategori ' . $item['nama'],
            ]);
        }
    }
}
