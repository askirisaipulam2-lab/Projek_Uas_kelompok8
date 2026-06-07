<?php

namespace App\Filament\Resources\LaporanKehilanganResource\Pages;

use App\Filament\Resources\LaporanKehilanganResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaporanKehilangans extends ListRecords
{
    protected static string $resource = LaporanKehilanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
