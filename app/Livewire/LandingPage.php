<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class LandingPage extends Component
{
    use WithPagination;

    // State untuk menyimpan pencarian dan filter kategori aktif
    public $search = '';
    public $activeCategory = null;

    // Reset halaman ke nomor 1 jika user mengubah filter/pencarian
    public function updating($property)
    {
        if (in_array($property, ['search', 'activeCategory'])) {
            $this->resetPage();
        }
    }

    // Fungsi untuk mengubah kategori ketika di-klik di menu/navbar
    public function setCategory($categoryId = null)
    {
        $this->activeCategory = $categoryId;
    }

    public function render()
    {
        $categories = Category::all();
        $posts = Post::query()->where('is_published', true)->latest()->paginate(6);

        return view('livewire.landing-page', [
            'posts' => $posts,
            'categories' => $categories
        ])->layout('layouts.app'); // 🔍 Beritahu Livewire untuk pakai folder layouts biasa, bukan components.layouts
    }

}