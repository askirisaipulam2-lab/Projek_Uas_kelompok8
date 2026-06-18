<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaporanTemuan extends Model
{
    protected $table = 'laporan_temuans';

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

    protected $casts = [
        'tanggal_temuan' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function klaims(): HasMany
    {
        return $this->hasMany(Klaim::class, 'laporan_temuan_id');
    }
}