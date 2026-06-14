<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * Menentukan apakah user bisa melihat daftar menu Posts di Filament.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Menentukan apakah user bisa melihat detail sebuah post.
     */
    public function view(User $user, Post $post): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Menentukan apakah user bisa membuat post baru.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Menentukan apakah user bisa mengedit/mengubah post.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Menentukan apakah user bisa menghapus post.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->role === 'admin';
    }
}