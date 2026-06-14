<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;

class SiberangComponent extends Component
{
    // DI SINI tempat meletakkan variabel search
    public $search = '';
    public $activeCategory = null;

    public function setCategory($id)
    {
        $this->activeCategory = $id;
    }

    public function render()
    {
        // Logika query Anda tetap di sini
        $posts = Post::query()
            ->when($this->activeCategory, function ($query) {
                $query->where('category_id', $this->activeCategory);
            })
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->get();

        return view('livewire.siberang-component', [
            'posts' => $posts
        ]);

        $posts = Post::all();

        // dd($posts); // Buka komen ini jika ingin melihat isi database di layar

        return view('livewire.siberang-component', [
            'posts' => $posts
        ]);
    }
}