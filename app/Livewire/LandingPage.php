<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class LandingPage extends Component
{
    use WithPagination;

    public $search = '';
    public $activeCategory = null;

    // Reset ke halaman 1 saat user mengetik atau ganti kategori
    public function updating($property)
    {
        if (in_array($property, ['search', 'activeCategory'])) {
            $this->resetPage();
        }
    }

    public function setCategory($categoryId = null)
    {
        $this->activeCategory = $categoryId;
    }

    public function render()
    {
        $categories = Category::all();

        // Query dinamis berdasarkan search dan activeCategory
        $posts = Post::query()
            ->where('is_published', true)
            ->when($this->activeCategory, function($query) {
                $query->where('category_id', $this->activeCategory);
            })
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('content', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(6);

        return view('livewire.landing-page', [
            'posts' => $posts,
            'categories' => $categories
        ])->layout('layouts.app');
    }
}