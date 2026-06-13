<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class StatistikChart extends ChartWidget
{
    // Mengubah heading agar terlihat lebih profesional
    protected static ?string $heading = 'Analisis Statistik Laporan';
    
    // Mengatur tinggi maksimal agar proporsional di grid dashboard Filament
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Total Laporan',
                    'data' => [5, 8, 12, 7, 15, 10],
                    'borderColor' => '#3B82F6', // Warna Royal Blue modern
                    'backgroundColor' => 'rgba(59, 130, 246, 0.05)', // Efek gradien transparan di bawah garis
                    'fill' => true, // Mengubah line-chart biasa menjadi area-chart yang estetik
                    'borderWidth' => 2.5, // Ketebalan garis kurva yang elegan
                    'pointBackgroundColor' => '#3B82F6',
                    'pointBorderColor' => '#FFFFFF', // Border putih tipis agar bulatan bawah terlihat tajam
                    'pointBorderWidth' => 1.5,
                    'pointRadius' => 3.5, // Ukuran bulatan grafik bawah yang pas (tidak kekecilan/kegedean)
                    'pointHoverRadius' => 6, // Membesar secara halus saat kursor mendekat
                    'tension' => 0.35, // Membuat garis patah-patah menjadi melengkung halus (smooth curve)
                ],
            ],
            'labels' => [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'Mei',
                'Jun',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * Konfigurasi Tambahan untuk Mengoptimalkan UI/UX Chart.js
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'labels' => [
                        'usePointStyle' => true, // Mengubah kotak legenda menjadi lingkaran
                        'boxWidth' => 3,         // 🔍 KECIL: Bulatan legenda atas dibuat mikro agar teks terlihat jelas
                        'boxHeight' => 3,
                        'padding' => 20,
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
                    'backgroundColor' => '#1E293B', // Tooltip dengan background gelap yang clean kontras
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false, // Menghilangkan garis vertikal di latar belakang agar rapi
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 11,
                        ],
                    ],
                ],
                'y' => [
                    'grid' => [
                        'color' => 'rgba(241, 245, 249, 0.4)', // Garis horizontal abu-abu tipis transparan
                    ],
                    'ticks' => [
                        'precision' => 0, // Mencegah angka pecahan/desimal muncul pada sumbu Y
                        'font' => [
                            'size' => 11,
                        ],
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false, // Tooltip otomatis aktif saat kursor berada di area dekat grafik (tanpa perlu pas di titik data)
            ],
            'animation' => [
                'duration' => 600, // Durasi animasi render grafik yang smooth saat dashboard dimuat
                'easing' => 'easeOutQuart',
            ],
        ];
    }
}