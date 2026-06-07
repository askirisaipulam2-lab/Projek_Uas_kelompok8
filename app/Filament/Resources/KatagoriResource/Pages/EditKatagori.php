<?php

namespace App\Filament\Resources\KatagoriResource\Pages;

use App\Filament\Resources\KatagoriResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKatagori extends EditRecord
{
    protected static string $resource = KatagoriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
