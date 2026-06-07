<?php

namespace App\Filament\Resources\LaporanTemuanResource\Pages;

use App\Filament\Resources\LaporanTemuanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanTemuan extends EditRecord
{
    protected static string $resource = LaporanTemuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
