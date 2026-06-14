<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class StatistikChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Lokasi Terbanyak';
    protected static ?string $maxHeight = '250px';

    protected function getData(): array
    {
        // Pastikan 'lokasi_id' disesuaikan dengan nama kolom di database Anda
        $data = DB::table('posts')
            ->join('lokasis', 'posts.id', '=', 'lokasis.id')
            ->select('lokasis.nama', DB::raw('COUNT(posts.id) as total'))
            ->groupBy('lokasis.nama')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->pluck('total', 'nama')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Laporan',
                    'data' => array_values($data),
                    // Warna elegan dengan transparansi sedikit agar lebih "soft"
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)', 
                        'rgba(16, 185, 129, 0.8)', 
                        'rgba(245, 158, 11, 0.8)', 
                        'rgba(139, 92, 246, 0.8)', 
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    'borderWidth' => 0, // Menghilangkan garis hitam sama sekali
                    'hoverOffset' => 12,
                    'spacing' => 5, // Memberi jarak antar irisan agar terlihat lebih eksklusif
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'cutout' => '82%', // Semakin tipis, semakin terlihat modern
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                    'labels' => [
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                        'padding' => 20,
                        'font' => ['size' => 9, 'weight' => '500'],
                    ],
                ],
                'tooltip' => [
                    'backgroundColor' => '#1f2937', // Dark tooltip agar elegan
                    'padding' => 12,
                    'cornerRadius' => 10,
                ],
            ],
            // Animasi masuk yang halus
            'animation' => [
                'animateScale' => true,
                'animateRotate' => true,
            ],
        ];
    }
}