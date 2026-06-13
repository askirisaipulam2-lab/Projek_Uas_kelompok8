<?php

namespace App\Filament\Widgets; // 🔍 SUDAH DIPERBAIKI: Namespace disesuaikan dengan standar Filament v3

use App\Models\Klaim;
use App\Models\LaporanKehilangan;
use App\Models\LaporanTemuan;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatistikOverview extends StatsOverviewWidget
{
    // Mengatur agar statistik diperbarui otomatis setiap 15 detik tanpa refresh halaman
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        // 1. Hitung data hari ini
        $hariIniKehilangan = LaporanKehilangan::whereDate('created_at', today())->count();
        $hariIniTemuan = LaporanTemuan::whereDate('created_at', today())->count();
        $totalHariIni = $hariIniKehilangan + $hariIniTemuan;

        // 2. Ambil data historis 7 hari terakhir untuk grafik mini (Sparkline)
        $trenKehilangan = [];
        $trenTemuan = [];
        $trenKlaim = [];

        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i)->toDateString();
            $trenKehilangan[] = LaporanKehilangan::whereDate('created_at', $tanggal)->count();
            $trenTemuan[] = LaporanTemuan::whereDate('created_at', $tanggal)->count();
            $trenKlaim[] = Klaim::whereDate('created_at', $tanggal)->count();
        }

        // 3. Logika indikator naik/turun laporan kehilangan
        $kemarinKehilangan = LaporanKehilangan::whereDate('created_at', now()->subDay())->count();
        $selisihKehilangan = $hariIniKehilangan - $kemarinKehilangan;
        $descKehilangan = $selisihKehilangan >= 0 
            ? "+" . $selisihKehilangan . " dari kemarin" 
            : $selisihKehilangan . " dari kemarin";

        return [
            // KOTAK 1: LAPORAN KEHILANGAN
            Stat::make('Laporan Kehilangan', LaporanKehilangan::count() . ' Barang')
                ->description($descKehilangan)
                ->descriptionIcon($selisihKehilangan >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($trenKehilangan) // Menampilkan grafik garis mini di bawah angka
                ->color($selisihKehilangan > 0 ? 'danger' : 'gray'),

            // KOTAK 2: LAPORAN TEMUAN
            Stat::make('Laporan Temuan', LaporanTemuan::count() . ' Barang')
                ->description('Barang berhasil diamankan')
                ->descriptionIcon('heroicon-m-check-badge')
                ->chart($trenTemuan)
                ->color('success'),

            // KOTAK 3: KLAIM BARANG
            Stat::make('Klaim Barang', Klaim::count() . ' Pengajuan')
                ->description('Butuh proses verifikasi')
                ->descriptionIcon('heroicon-m-document-magnifying-glass')
                ->chart($trenKlaim)
                ->color('warning'),

            // KOTAK 4: AKTIVITAS HARI INI
            Stat::make('Laporan Baru Hari Ini', $totalHariIni . ' Laporan')
                ->description("Kehilangan: {$hariIniKehilangan} • Temuan: {$hariIniTemuan}")
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
        ];
    }
}