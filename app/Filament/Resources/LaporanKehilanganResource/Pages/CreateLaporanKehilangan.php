<?php

namespace App\Filament\Resources\LaporanKehilanganResource\Pages;

use App\Filament\Resources\LaporanKehilanganResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Notifikasi;
use App\Models\User;
use Filament\Notifications\Notification as NavbarNotification;

class CreateLaporanKehilangan extends CreateRecord
{
    protected static string $resource = LaporanKehilanganResource::class;

    protected function afterCreate(): void
    {
        $kehilangan = $this->record; // Mengambil data kehilangan yang baru disimpan
        $judulNotif = "Laporan Kehilangan Baru: " . $kehilangan->nama_barang;
        $pesanNotif = "Telah dilaporkan kehilangan barang '{$kehilangan->nama_barang}' di lokasi {$kehilangan->lokasi}. Mohon hubungi pihak admin jika melihatnya.";

        // Opsi A: Broadcast ke semua user (Mahasiswa & Admin)
        $users = User::all();

        foreach ($users as $user) {
            // 1. Simpan ke Tabel Log Arsip (Model Notifikasi Anda)
            Notifikasi::create([
                'user_id' => $user->id,
                'judul' => $judulNotif,
                'pesan' => $pesanNotif,
                'is_read' => false,
            ]);

            // 2. Tembak langsung ke Lonceng Bel Navbar secara real-time
            NavbarNotification::make()
                ->title($judulNotif)
                ->body($pesanNotif)
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->sendToDatabase($user);
        }
    }
}