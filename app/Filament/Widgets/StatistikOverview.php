<?php

namespace App\Filament\Widgets;

use App\Models\Klaim;
use App\Models\LaporanKehilangan;
use App\Models\LaporanTemuan;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatistikOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(
                'Laporan Kehilangan',
                LaporanKehilangan::count()
            ),

            Stat::make(
                'Laporan Temuan',
                LaporanTemuan::count()
            ),

            Stat::make(
                'Klaim Barang',
                Klaim::count()
            ),

            Stat::make(
                'Hari Ini',
                LaporanKehilangan::whereDate(
                    'created_at',
                    today()
                )->count()
            ),
        ];
    }
}