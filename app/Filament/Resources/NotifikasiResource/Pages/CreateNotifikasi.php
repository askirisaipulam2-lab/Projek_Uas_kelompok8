<?php

namespace App\Filament\Resources\NotifikasiResource\Pages;

use App\Filament\Resources\NotifikasiResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification; // <-- Import ini

class CreateNotifikasi extends CreateRecord
{
    protected static string $resource = NotifikasiResource::class;

    // Fungsi otomatis berjalan setelah data notifikasi baru berhasil dibuat
    protected function afterCreate(): void
    {
        $notifikasi = $this->record;

        // Kirimkan ke lonceng navbar user penerima
        Notification::make()
            ->title($notifikasi->judul)
            ->body($notifikasi->pesan)
            ->icon('heroicon-o-bell')
            ->iconColor('success')
            ->sendToDatabase($notifikasi->user); // Mengirim ke relasi user
    }
}