<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanTemuan extends Model
{
    protected $fillable = [
        'user_id',
        'kategori_id',
        'lokasi_id',
        'judul',
        'deskripsi',
        'tanggal_temuan',
        'gambar',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function klaims()
    {
        return $this->hasMany(Klaim::class);
    }
}
