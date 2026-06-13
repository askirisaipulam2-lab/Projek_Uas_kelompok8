<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

// 1. Tambahkan import kontrak HasAvatar dari Filament
use Filament\Models\Contracts\HasAvatar;

// 2. Hubungkan class dengan implements HasAvatar
class User extends Authenticatable implements HasAvatar
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'foto',
        'nomor_hp',
        'nim',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Fungsi ini sekarang akan otomatis dipanggil oleh Navbar Filament
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : 'https://ui-avatars.com/api/?background=random&color=fff&name=' . urlencode($this->name);
    }
}