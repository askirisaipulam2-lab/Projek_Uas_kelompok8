<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tag;
use App\Models\Notifikasi;

class LaporanKehilangan extends Model
{
    protected $fillable = [
        'user_id',
        'kategori_id',
        'lokasi_id',
        'judul',
        'deskripsi',
        'tanggal_hilang',
        'gambar',
        'status',
    ];

    protected static function booted(): void
    {
        static::created(function ($laporan) {

            Notifikasi::create([
                'user_id' => $laporan->user_id,
                'judul' => 'Laporan Kehilangan',
                'pesan' => 'Laporan kehilangan "' . $laporan->judul . '" berhasil dibuat.',
                'is_read' => false,
            ]);

        });
    }

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

    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            'laporan_kehilangan_tag'
        );
    }
}