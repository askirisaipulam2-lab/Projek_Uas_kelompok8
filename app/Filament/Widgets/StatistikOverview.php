<?php

namespace App\Filament\Widgets;

use App\Models\Klaim;
use App\Models\LaporanKehilangan;
use App\Models\LaporanTemuan;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatistikOverview extends StatsOverviewWidget
{
    // Opsional: Mengatur agar statistik diperbarui otomatis setiap 10 detik tanpa refresh halaman
    protected static ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        // Hitung total laporan hari ini dari gabungan Kehilangan dan Temuan
        $hariIniKehilangan = LaporanKehilangan::whereDate('created_at', today())->count();
        $hariIniTemuan = LaporanTemuan::whereDate('created_at', today())->count();
        $totalHariIni = $hariIniKehilangan + $hariIniTemuan;

        return [
            Stat::make('Laporan Kehilangan', LaporanKehilangan::count())
                ->description('Total barang hilang yang dilaporkan')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'), // Berwarna MERAH karena indikasi kehilangan barang

            Stat::make('Laporan Temuan', LaporanTemuan::count())
                ->description('Total barang yang berhasil ditemukan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'), // Berwarna HIJAU karena indikasi barang aman/ditemukan

            Stat::make('Klaim Barang', Klaim::count())
                ->description('Permintaan pencocokan pemilik')
                ->descriptionIcon('heroicon-m-document-magnifying-glass')
                ->color('warning'), // Berwarna KUNING/ORANYE karena butuh verifikasi/proses

            Stat::make('Laporan Baru Hari Ini', $totalHariIni)
                ->description("Kehilangan: {$hariIniKehilangan} | Temuan: {$hariIniTemuan}")
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'), // Berwarna BIRU untuk informasi umum/terkini
        ];
    }
}
