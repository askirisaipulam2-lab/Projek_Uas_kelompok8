<?php

namespace App\Filament\Resources\KlaimResource\Pages;

use App\Filament\Resources\KlaimResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKlaims extends ListRecords
{
    protected static string $resource = KlaimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
