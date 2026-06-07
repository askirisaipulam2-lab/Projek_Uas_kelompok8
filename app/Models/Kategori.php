<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    public function laporanKehilangans()
    {
        return $this->hasMany(LaporanKehilangan::class);
    }

    public function laporanTemuans()
    {
        return $this->hasMany(LaporanTemuan::class);
    }
}