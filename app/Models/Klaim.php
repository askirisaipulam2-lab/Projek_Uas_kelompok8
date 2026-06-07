<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Klaim extends Model
{
    protected $fillable = [
        'laporan_temuan_id',
        'user_id',
        'bukti_kepemilikan',
        'status',
    ];

    public function laporanTemuan()
    {
        return $this->belongsTo(LaporanTemuan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
