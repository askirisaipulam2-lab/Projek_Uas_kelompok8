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

        $namaBarang = $temuan->judul;

        $namaLokasi = $temuan->lokasi?->nama ?? 'Lokasi tidak diketahui';

        $judulNotif = "Barang Ditemukan: {$namaBarang}";

        $pesanNotif = "Kabar baik! Telah ditemukan barang '{$namaBarang}' di sekitar {$namaLokasi}. Silakan cek detailnya pada menu Laporan Temuan.";

        // Kirim ke seluruh user
        $users = User::all();

        foreach ($users as $user) {

            // Simpan ke tabel notifikasis
            Notifikasi::create([
                'user_id' => $user->id,
                'judul' => $judulNotif,
                'pesan' => $pesanNotif,
                'is_read' => false,
            ]);

            // Kirim ke lonceng navbar Filament
            NavbarNotification::make()
                ->title($judulNotif)
                ->body($pesanNotif)
                ->icon('heroicon-o-magnifying-glass')
                ->color('success')
                ->sendToDatabase($user);
        }
    }
}