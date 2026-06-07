<?php

namespace App\Filament\Resources\LaporanKehilanganResource\Pages;

use App\Filament\Resources\LaporanKehilanganResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanKehilangan extends EditRecord
{
    protected static string $resource = LaporanKehilanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
