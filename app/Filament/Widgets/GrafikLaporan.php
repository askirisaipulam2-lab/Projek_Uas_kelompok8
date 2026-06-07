<?php

namespace App\Filament\Widgets;

use App\Models\Klaim;
use App\Models\LaporanKehilangan;
use App\Models\LaporanTemuan;
use Filament\Widgets\ChartWidget;

class GrafikLaporan extends ChartWidget
{
    protected static ?string $heading = 'Grafik Laporan Barang';

    public ?string $filter = 'bulan';

    protected function getFilters(): ?array
    {
        return [
            'minggu' => 'Minggu Ini',
            'bulan' => 'Bulan Ini',
            'tahun' => 'Tahun Ini',
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

                $namaHari = [
                    'Minggu',
                    'Senin',
                    'Selasa',
                    'Rabu',
                    'Kamis',
                    'Jumat',
                    'Sabtu',
                ];

                for ($i = 6; $i >= 0; $i--) {

                    $tanggal = now()->subDays($i);

                    $labels[] = $namaHari[$tanggal->dayOfWeek];

                    $kehilangan[] = LaporanKehilangan::whereDate(
                        'created_at',
                        $tanggal->toDateString()
                    )->count();

                    $temuan[] = LaporanTemuan::whereDate(
                        'created_at',
                        $tanggal->toDateString()
                    )->count();

                    $klaim[] = Klaim::whereDate(
                        'created_at',
                        $tanggal->toDateString()
                    )->count();
                }

                break;

            case 'tahun':

                $tahun = now()->year;

                $labels = [
                    "Jan $tahun",
                    "Feb $tahun",
                    "Mar $tahun",
                    "Apr $tahun",
                    "Mei $tahun",
                    "Jun $tahun",
                    "Jul $tahun",
                    "Ags $tahun",
                    "Sep $tahun",
                    "Okt $tahun",
                    "Nov $tahun",
                    "Des $tahun",
                ];

                for ($bulan = 1; $bulan <= 12; $bulan++) {

                    $kehilangan[] = LaporanKehilangan::query()
                        ->whereYear('created_at', $tahun)
                        ->whereMonth('created_at', $bulan)
                        ->count();

                    $temuan[] = LaporanTemuan::query()
                        ->whereYear('created_at', $tahun)
                        ->whereMonth('created_at', $bulan)
                        ->count();

                    $klaim[] = Klaim::query()
                        ->whereYear('created_at', $tahun)
                        ->whereMonth('created_at', $bulan)
                        ->count();
                }

                break;

            default:

                $bulan = now()->month;
                $tahun = now()->year;

                for ($minggu = 1; $minggu <= 4; $minggu++) {

                    $awal = now()
                        ->setMonth($bulan)
                        ->setYear($tahun)
                        ->startOfMonth()
                        ->addWeeks($minggu - 1);

                    $akhir = (clone $awal)->copy()->addDays(6);

                    $labels[] = 'Minggu ' . $minggu;

                    $kehilangan[] = LaporanKehilangan::whereBetween(
                        'created_at',
                        [$awal, $akhir]
                    )->count();

                    $temuan[] = LaporanTemuan::whereBetween(
                        'created_at',
                        [$awal, $akhir]
                    )->count();

                    $klaim[] = Klaim::whereBetween(
                        'created_at',
                        [$awal, $akhir]
                    )->count();
                }

                break;
        }

        return [

            'datasets' => [

                [
                    'label' => 'Laporan Kehilangan',
                    'data' => $kehilangan,
                    'borderColor' => '#EAB308',
                    'backgroundColor' => '#EAB308',
                    'borderWidth' => 3,
                    'pointRadius' => 5,
                    'tension' => 0.4,
                ],

                [
                    'label' => 'Laporan Temuan',
                    'data' => $temuan,
                    'borderColor' => '#22C55E',
                    'backgroundColor' => '#22C55E',
                    'borderWidth' => 3,
                    'pointRadius' => 5,
                    'tension' => 0.4,
                ],

                [
                    'label' => 'Klaim Barang',
                    'data' => $klaim,
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => '#3B82F6',
                    'borderWidth' => 3,
                    'pointRadius' => 5,
                    'tension' => 0.4,
                ],

            ],

            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}