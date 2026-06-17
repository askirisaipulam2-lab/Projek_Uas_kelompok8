<?php

namespace Database\Seeders;

use App\Models\PostCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Kehilangan',
            'Temuan',
            'Edukasi',
            'Pengumuman',
        ];

        foreach ($categories as $category) {
            PostCategory::create([
                'name' => $category,
                'slug' => Str::slug($category),
            ]);
        }
    }
}
