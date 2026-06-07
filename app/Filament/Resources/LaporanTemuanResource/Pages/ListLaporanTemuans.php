<?php

namespace App\Filament\Resources\LaporanTemuanResource\Pages;

use App\Filament\Resources\LaporanTemuanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaporanTemuans extends ListRecords
{
    protected static string $resource = LaporanTemuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
