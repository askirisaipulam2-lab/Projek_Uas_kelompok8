<?php

namespace App\Filament\Resources\LaporanTemuanResource\Pages;

use App\Filament\Resources\LaporanTemuanResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Notifikasi;
use App\Models\User;
use Filament\Notifications\Notification as NavbarNotification;

class CreateLaporanTemuan extends CreateRecord
{
    protected static string $resource = LaporanTemuanResource::class;

    protected function afterCreate(): void
    {
        $temuan = $this->record;
        $judulNotif = "Barang Ditemukan: " . $temuan->nama_barang;
        $pesanNotif = "Kabar baik! Telah ditemukan barang '{$temuan->nama_barang}' di sekitar {$temuan->lokasi}. Silakan cek detailnya di menu Temuan Barang.";

        $users = User::all();

        foreach ($users as $user) {
            // 1. Simpan ke Tabel Log Arsip
            Notifikasi::create([
                'user_id' => $user->id,
                'judul' => $judulNotif,
                'pesan' => $pesanNotif,
                'is_read' => false,
            ]);

            // 2. Kirim ke Lonceng Navbar
            NavbarNotification::make()
                ->title($judulNotif)
                ->body($pesanNotif)
                ->icon('heroicon-o-magnifying-glass')
                ->color('success')
                ->sendToDatabase($user);
        }
    }
}