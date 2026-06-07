<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'nama',
    ];

    public function laporanKehilangans()
    {
        return $this->belongsToMany(
            LaporanKehilangan::class,
            'laporan_kehilangan_tag'
        );
    }
}