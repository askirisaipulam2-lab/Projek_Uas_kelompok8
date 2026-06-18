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
        $kehilangan = $this->record;

        $judulNotif = "Laporan Kehilangan Baru";
        $pesanNotif = "Laporan kehilangan '{$kehilangan->judul}' berhasil dibuat.";

        // Notifikasi ke pembuat laporan
        Notifikasi::create([
            'user_id' => $kehilangan->user_id,
            'judul' => $judulNotif,
            'pesan' => $pesanNotif,
            'is_read' => false,
        ]);

        NavbarNotification::make()
            ->title($judulNotif)
            ->body($pesanNotif)
            ->icon('heroicon-o-exclamation-triangle')
            ->color('danger')
            ->sendToDatabase($kehilangan->user);

        // Jika pembuat adalah mahasiswa, kirim juga ke admin
        if ($kehilangan->user->role === 'mahasiswa') {

            $admins = User::where('role', 'admin')->get();

            foreach ($admins as $admin) {

                Notifikasi::create([
                    'user_id' => $admin->id,
                    'judul' => 'Laporan Kehilangan Baru',
                    'pesan' => $kehilangan->user->name . ' membuat laporan kehilangan "' . $kehilangan->judul . '".',
                    'is_read' => false,
                ]);

                NavbarNotification::make()
                    ->title('Laporan Kehilangan Baru')
                    ->body($kehilangan->user->name . ' membuat laporan kehilangan "' . $kehilangan->judul . '".')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning')
                    ->sendToDatabase($admin);
            }
        }
    }
}