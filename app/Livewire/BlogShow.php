<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;

class BlogShow extends Component
{
    public $post;

    // Fungsi mount akan otomatis berjalan saat halaman diakses membawa parameter slug
    public function mount($slug)
    {
        $this->post = Post::with('postCategory') // Optimasi agar data kategori langsung ikut terbawa tanpa memicu eror null
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail(); // Jika slug salah, otomatis melempar eror 404 (Halaman Tidak Ditemukan)
    }

    public function render()
    {
        return view('livewire.blog-show')
            ->layout('layouts.app'); // Perbaikan: diarahkan langsung ke file induk layout proyekmu (layouts/app.blade.php)
    }
}