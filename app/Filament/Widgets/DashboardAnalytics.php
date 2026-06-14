<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Lokasi;
use App\Models\Tag;
use App\Models\Kategori;
use Filament\Widgets\ChartWidget;

class DashboardAnalytics extends ChartWidget
{
    protected static ?string $heading = 'Statistik Data Utama';

    protected static ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = 2;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Data',
                    'data' => [
                        User::count(),
                        Lokasi::count(),
                        Tag::count(),
                        Kategori::count(),
                    ],

                    'backgroundColor' => [
                        '#3B82F6',
                        '#8B5CF6',
                        '#F59E0B',
                        '#10B981',
                    ],

                    'borderRadius' => 12,
                    'borderSkipped' => false,
                    'barThickness' => 18,
                ],
            ],

            'labels' => [
                ' User',
                ' Lokasi',
                ' Tag',
                ' Kategori',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',

            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],

            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'display' => false,
                    ],
                    'border' => [
                        'display' => false,
                    ],
                ],

                'y' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'border' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 13,
                            'weight' => 'bold',
                        ],
                    ],
                ],
            ],
        ];
    }
}