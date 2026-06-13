<?php

namespace App\Filament\Widgets;

use App\Models\Klaim;
use App\Models\LaporanKehilangan;
use App\Models\LaporanTemuan;
use Filament\Widgets\ChartWidget;

class GrafikLaporan extends ChartWidget
{
    protected static ?string $heading = 'Grafik Analisis Laporan Barang';
    protected static ?string $maxHeight = '340px';
    public ?string $filter = 'bulan';
    protected static ?string $pollingInterval = '30s';

    protected function getFilters(): ?array
    {
        return [
            'minggu' => '📅 Minggu Ini',
            'bulan' => '📊 Bulan Ini',
            'tahun' => '📈 Tahun Ini',
        ];
    }

    protected function getData(): array
    {
        $kehilangan = [];
        $temuan = [];
        $klaim = [];
        $labels = [];

        switch ($this->filter) {
            case 'minggu':
                $namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                for ($i = 6; $i >= 0; $i--) {
                    $tanggal = now()->subDays($i);
                    $labels[] = $namaHari[$tanggal->dayOfWeek];
                    $kehilangan[] = LaporanKehilangan::whereDate('created_at', $tanggal->toDateString())->count();
                    $temuan[] = LaporanTemuan::whereDate('created_at', $tanggal->toDateString())->count();
                    $klaim[] = Klaim::whereDate('created_at', $tanggal->toDateString())->count();
                }
                break;

            case 'tahun':
                $tahun = now()->year;
                $labels = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"];
                for ($bulan = 1; $bulan <= 12; $bulan++) {
                    $kehilangan[] = LaporanKehilangan::whereYear('created_at', $tahun)->whereMonth('created_at', $bulan)->count();
                    $temuan[] = LaporanTemuan::whereYear('created_at', $tahun)->whereMonth('created_at', $bulan)->count();
                    $klaim[] = Klaim::whereYear('created_at', $tahun)->whereMonth('created_at', $bulan)->count();
                }
                break;

            default:
                $bulan = now()->month;
                $tahun = now()->year;
                for ($minggu = 1; $minggu <= 4; $minggu++) {
                    $awal = now()->setMonth($bulan)->setYear($tahun)->startOfMonth()->addWeeks($minggu - 1);
                    $akhir = (clone $awal)->addDays(6);
                    $labels[] = 'Minggu ' . $minggu;
                    $kehilangan[] = LaporanKehilangan::whereBetween('created_at', [$awal, $akhir])->count();
                    $temuan[] = LaporanTemuan::whereBetween('created_at', [$awal, $akhir])->count();
                    $klaim[] = Klaim::whereBetween('created_at', [$awal, $akhir])->count();
                }
                break;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Laporan Kehilangan',
                    'data' => $kehilangan,
                    'borderColor' => '#F59E0B',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.05)',
                    'fill' => true,
                    'borderWidth' => 2.5,
                    'pointBackgroundColor' => '#F59E0B',
                    'pointBorderColor' => '#FFFFFF', // Efek border putih agar titik di bawah terlihat tajam dan kontras
                    'pointBorderWidth' => 1.5,
                    'pointRadius' => 3.5, // 🔍 DISEDANGKAN: Bulatan pada grafik di bawah terlihat pas
                    'pointHoverRadius' => 6, // 🔍 Membesar secara proporsional saat di-hover
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Laporan Temuan',
                    'data' => $temuan,
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.05)',
                    'fill' => true,
                    'borderWidth' => 2.5,
                    'pointBackgroundColor' => '#10B981',
                    'pointBorderColor' => '#FFFFFF',
                    'pointBorderWidth' => 1.5,
                    'pointRadius' => 3.5, // 🔍 DISEDANGKAN
                    'pointHoverRadius' => 6,
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Klaim Barang',
                    'data' => $klaim,
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.05)',
                    'fill' => true,
                    'borderWidth' => 2.5,
                    'pointBackgroundColor' => '#3B82F6',
                    'pointBorderColor' => '#FFFFFF',
                    'pointBorderWidth' => 1.5,
                    'pointRadius' => 3.5, // 🔍 DISEDANGKAN
                    'pointHoverRadius' => 6,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'labels' => [
                        'usePointStyle' => true,
                        'boxWidth' => 3,  // 🔍 TETAP KECIL: Bulatan penanda teks di atas agar tidak menutupi huruf
                        'boxHeight' => 3,
                        'padding' => 24,
                        'font' => [
                            'size' => 12,
                            'weight' => '500',
                        ],
                    ],
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                    'padding' => 10,
                    'cornerRadius' => 6,
                    'backgroundColor' => '#1E293B',
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 11,
                        ],
                    ],
                ],
                'y' => [
                    'grid' => [
                        'color' => 'rgba(241, 245, 249, 0.4)',
                    ],
                    'ticks' => [
                        'precision' => 0,
                        'font' => [
                            'size' => 11,
                        ],
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
            'animation' => [
                'duration' => 600,
                'easing' => 'easeOutQuart',
            ],
        ];
    }
}