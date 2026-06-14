<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'title',
        'slug',
        'category_id', // Sesuaikan dengan database fisik
        'content',
        'image',
        'is_published'
    ];

    public function postCategory(): BelongsTo
    {
        // Hubungkan model ini ke tabel post_categories menggunakan foreign key 'category_id'
        return $this->belongsTo(PostCategory::class, 'category_id');
    }
}