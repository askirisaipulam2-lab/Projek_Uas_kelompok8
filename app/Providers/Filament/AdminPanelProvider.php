<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color; // <-- 1. PASTIKAN LINE INI SUDAH DI-IMPORT
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css; // Gunakan Css untuk file CSS
use Filament\Support\Assets\Js;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            // Menambahkan Logo khusus untuk Mode Gelap (opsional)
            ->brandLogo(asset('images/NF.png'))

            // Mengatur tinggi logo agar proporsional
            ->brandLogoHeight('3rem')
            // --- BAGIAN WARNA ---
            ->colors([
                'primary' => Color::Emerald, // Mengubah warna tombol dan fokus menjadi hijau
                'gray' => Color::Slate,     // Mengubah nuansa warna teks/background abu-abu
                
            ])
            ->assets([
                Css::make('custom-css', asset('css/filament-custom.css')),
            ])
            // --- 🚀 PASANG DATABASE NOTIFICATIONS DI SINI ---
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s') // (Opsional) Cek notifikasi baru otomatis ke server setiap 30 detik
            // ------------------------------------------------
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);

    }
}