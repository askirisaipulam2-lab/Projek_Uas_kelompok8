<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostCategory extends Model
{
    use HasFactory;

    protected $table = 'post_categories';

    protected $fillable = [
        'name',
        'slug',
    ];

    public function posts(): HasMany
    {
        // Beritahu Laravel kalau foreign key di tabel posts bernama 'category_id'
        return $this->hasMany(Post::class, 'category_id');
    }
}