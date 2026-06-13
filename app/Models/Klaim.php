<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Notifikasi;
use App\Models\LaporanTemuan;
use App\Models\LaporanKehilangan;

class Klaim extends Model
{
    protected $fillable = [
        'laporan_temuan_id',
        'user_id',
        'bukti_kepemilikan',
        'foto_bukti', // 👈 WAJIB TAMBAHKAN BARIS INI
        'status',
    ];

    protected static function booted(): void
    {
        static::updated(function ($klaim) {

            // Hanya dijalankan jika status berubah
            if ($klaim->isDirty('status')) {

                Notifikasi::create([
                    'user_id' => $klaim->user_id,
                    'judul'   => 'Status Klaim Diperbarui',
                    'pesan'   => 'Status klaim Anda sekarang: ' . strtoupper($klaim->status),
                    'is_read' => false,
                ]);

                if ($klaim->status === 'disetujui') {

                    // Update laporan temuan
                    $laporanTemuan = $klaim->laporanTemuan;

                    if ($laporanTemuan) {

                        $laporanTemuan->update([
                            'status' => 'diklaim',
                        ]);

                        $laporanKehilangan = LaporanKehilangan::where(
                            'kategori_id',
                            $laporanTemuan->kategori_id
                        )->where(
                            'status',
                            'hilang'
                        )->first();

                        if ($laporanKehilangan) {

                            $laporanKehilangan->update([
                                'status' => 'diklaim',
                            ]);
                        }
                    }
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function laporanTemuan()
    {
        return $this->belongsTo(
            LaporanTemuan::class,
            'laporan_temuan_id'
        );
    }
}