<?php

namespace App\Filament\Resources\KlaimResource\Pages;

use App\Filament\Resources\KlaimResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Notifikasi;
use App\Models\User;
use Filament\Notifications\Notification as NavbarNotification;

class CreateKlaim extends CreateRecord
{
    protected static string $resource = KlaimResource::class;

    protected function afterCreate(): void
    {
        $klaim = $this->record; // Mengambil relasi data klaim
        
        // Asumsi model Klaim memiliki relasi 'user_id' (si pengaju) dan 'temuan' (barang yang diklaim)
        $mahasiswaPengaju = $klaim->user; 
        $namaBarang = $klaim->temuan?->nama_barang ?? 'Barang Inventaris';

        $judulNotif = "Pengajuan Klaim Diproses";
        $pesanNotif = "Pengajuan klaim Anda untuk barang '{$namaBarang}' telah berhasil diajukan. Mohon tunggu proses verifikasi berkas oleh Admin.";

        // --- 1. KIRIM KE MAHASISWA PENGEKLAIM ---
        Notifikasi::create([
            'user_id' => $mahasiswaPengaju->id,
            'judul' => $judulNotif,
            'pesan' => $pesanNotif,
            'is_read' => false,
        ]);

        NavbarNotification::make()
            ->title($judulNotif)
            ->body($pesanNotif)
            ->icon('heroicon-o-document-check')
            ->color('info')
            ->sendToDatabase($mahasiswaPengaju);


        // --- 2. KIRIM KE ADMIN (Agar Admin mendapat pemberitahuan ada tugas validasi baru) ---
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            $judulAdmin = "Permohonan Klaim Baru";
            $pesanAdmin = "Mahasiswa bernama {$mahasiswaPengaju->name} baru saja mengajukan klaim kepemilikan atas '{$namaBarang}'.";

            Notifikasi::create([
                'user_id' => $admin->id,
                'judul' => $judulAdmin,
                'pesan' => $pesanAdmin,
                'is_read' => false,
            ]);

            NavbarNotification::make()
                ->title($judulAdmin)
                ->body($pesanAdmin)
                ->icon('heroicon-o-shield-check')
                ->color('warning')
                ->sendToDatabase($admin);
        }
    }
}