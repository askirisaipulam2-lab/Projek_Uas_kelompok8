<?php

namespace App\Filament\Resources\NotifikasiResource\Pages;

use App\Filament\Resources\NotifikasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification; // <-- Import ini

class EditNotifikasi extends EditRecord
{
    protected static string $resource = NotifikasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    // Fungsi otomatis berjalan setelah Admin menekan tombol "Save Changes"
    protected function afterSave(): void
    {
        $notifikasi = $this->record;

        // Kirim sinyal pembaharuan ke lonceng navbar user
        Notification::make()
            ->title('Pemberitahuan Diperbarui')
            ->body("Ada perubahan pada notifikasi Anda: \"{$notifikasi->judul}\"")
            ->icon('heroicon-o-arrow-path')
            ->iconColor('warning')
            ->sendToDatabase($notifikasi->user);
    }
}