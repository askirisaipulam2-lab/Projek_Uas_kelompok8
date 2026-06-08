<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Notifikasi;

class LaporanTemuan extends Model
{
    // Opsional: Daftarkan nama tabel secara eksplisit jika Laravel salah mendeteksi jamak (plural)
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

    // Mengatur tipe data bawaan agar otomatis dikonversi oleh Laravel
    protected $casts = [
        'tanggal_temuan' => 'date',
    ];

    protected static function booted(): void
    {
        static::created(function ($laporan) {
            Notifikasi::create([
                'user_id' => $laporan->user_id,
                'judul'   => 'Laporan Temuan Berhasil', // DIPERBAIKI: Sebelumnya Laporan Kehilangan
                'pesan'   => 'Laporan temuan barang "' . $laporan->judul . '" berhasil dibuat.',
                'is_read' => false,
            ]);
        });
    }

    // Type-hinting BelongsTo ditambahkan untuk standar Laravel modern
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

    // Type-hinting HasMany ditambahkan
    public function klaims(): HasMany
    {
        return $this->hasMany(Klaim::class, 'laporan_temuan_id'); // Pastikan foreign key di tabel klaims sesuai
    }
}
