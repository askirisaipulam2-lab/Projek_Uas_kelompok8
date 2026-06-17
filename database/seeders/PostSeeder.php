<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'category' => 'Pengumuman',
                'title' => 'Panduan Pelaporan Barang Hilang di Kampus',
                'content' => '<p>Gunakan menu laporan kehilangan untuk mencatat detail barang, lokasi terakhir, tanggal kejadian, dan ciri khusus agar proses pencarian lebih mudah divalidasi.</p>',
            ],
            [
                'category' => 'Edukasi',
                'title' => 'Tips Menjaga Barang Pribadi Saat Beraktivitas',
                'content' => '<p>Simpan barang berharga di tempat yang mudah diawasi, beri tanda pengenal, dan segera laporkan jika menemukan barang yang bukan milik Anda.</p>',
            ],
            [
                'category' => 'Temuan',
                'title' => 'Barang Temuan Dapat Diklaim Melalui Sistem',
                'content' => '<p>Mahasiswa dapat mengajukan klaim dengan bukti kepemilikan yang jelas. Admin akan memverifikasi data sebelum status klaim disetujui.</p>',
            ],
            [
                'category' => 'Kehilangan',
                'title' => 'Lengkapi Ciri Khusus Saat Membuat Laporan',
                'content' => '<p>Ciri khusus seperti warna, merek, nomor seri, stiker, atau isi barang membantu petugas mencocokkan laporan kehilangan dengan data temuan.</p>',
            ],
        ];

        foreach ($posts as $post) {
            $category = PostCategory::where('name', $post['category'])->first();

            Post::create([
                'category_id' => $category->id,
                'title' => $post['title'],
                'slug' => Str::slug($post['title']),
                'content' => $post['content'],
                'is_published' => true,
            ]);
        }
    }
}
