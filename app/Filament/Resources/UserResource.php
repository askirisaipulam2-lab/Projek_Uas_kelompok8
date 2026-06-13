<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Users';
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        
                        // SISI KIRI: Kredensial Akun & Data Personal (Mengambil 2 Kolom)
                        Forms\Components\Section::make('Informasi Akun & Hak Akses')
                            ->description('Kelola detail profil pengguna, peranan sistem, dan nomor kontak aktif.')
                            ->icon('heroicon-o-user-circle')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->placeholder('Masukkan nama sesuai KTM / KTP')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('email')
                                    ->label('Alamat Email')
                                    ->placeholder('contoh@kampus.ac.id')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true),

                                Forms\Components\TextInput::make('password')
                                    ->label('Kata Sandi')
                                    ->placeholder(fn(string $operation): string => $operation === 'create' ? 'Minimal 8 karakter' : 'Kosongkan jika tidak ingin diubah')
                                    ->password()
                                    ->required(fn(string $operation): bool => $operation === 'create')
                                    ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                                    ->dehydrated(fn($state) => filled($state)),

                                Forms\Components\Select::make('role')
                                    ->label('Role Akses Sistem')
                                    ->options([
                                        'admin' => 'Admin',
                                        'mahasiswa' => 'Mahasiswa',
                                    ])
                                    ->default('mahasiswa')
                                    ->native(false)
                                    ->selectablePlaceholder(false)
                                    ->reactive()
                                    ->required(),

                                Forms\Components\TextInput::make('nim')
                                    ->label('NIM (Nomor Induk Mahasiswa)')
                                    ->placeholder('Masukkan NIM aktif')
                                    ->visible(fn(Forms\Get $get) => $get('role') === 'mahasiswa')
                                    ->required(fn(Forms\Get $get) => $get('role') === 'mahasiswa')
                                    ->maxLength(50)
                                    ->unique(ignoreRecord: true),

                                Forms\Components\TextInput::make('nomor_hp')
                                    ->label('Nomor HP / WhatsApp')
                                    ->placeholder('Contoh: 0812XXXXXXXX')
                                    ->tel()
                                    ->maxLength(20),
                            ])
                            ->columns(2)
                            ->columnSpan(2),

                        // SISI KANAN: Manajemen Foto Profil (Mengambil 1 Kolom)
                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\Section::make('Foto Profil')
                                    ->description('Gunakan foto formal/semi-formal untuk mempermudah verifikasi.')
                                    ->icon('heroicon-o-camera')
                                    ->schema([
                                        Forms\Components\FileUpload::make('foto')
                                            ->label('')
                                            ->avatar() // Mengubah UI kotak uploader menjadi lingkaran avatar premium
                                            ->image()
                                            ->imageEditor() // Fitur cropping lingkaran bawaan langsung di browser
                                            ->circleCropper() // Memaksa rasio potong berbentuk bulat sempurna
                                            ->disk('public')
                                            ->directory('users')
                                            ->fetchFileInformation(false)
                                            ->alignCenter(),
                                    ]),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom 1: Foto Bulat dengan fallback API UI-Avatars yang rapi
                Tables\Columns\ImageColumn::make('foto')
                    ->label('Profil')
                    ->circular()
                    ->height(45)
                    ->width(45)
                    ->extraImgAttributes(['class' => 'shadow-inner border border-gray-100'])
                    ->defaultImageUrl(fn ($record): string => 'https://ui-avatars.com/api/?background=6366f1&color=fff&name=' . urlencode($record->name)),

                // Kolom 2: Nama Utama + Sub-Deskripsi Email & NIM di bawahnya (Sangat Hemat Tempat)
                Tables\Columns\TextColumn::make('name')
                    ->label('Identitas Pengguna')
                    ->weight('bold')
                    ->searchable()
                    ->description(fn (User $record): string => 
                        $record->email . ($record->nim ? " • NIM: {$record->nim}" : "")
                    ),

                // Kolom 3: Kontak Telepon
                Tables\Columns\TextColumn::make('nomor_hp')
                    ->label('Kontak')
                    ->icon('heroicon-m-phone')
                    ->iconColor('gray')
                    ->placeholder('-')
                    ->searchable(),

                // Kolom 4: Badge Hak Akses Berikon Khusus
                Tables\Columns\TextColumn::make('role')
                    ->label('Akses')
                    ->badge()
                    ->icon(fn (string $state): string => match (strtolower($state)) {
                        'admin' => 'heroicon-m-shield-check',
                        'mahasiswa' => 'heroicon-m-academic-cap',
                        default => 'heroicon-m-user',
                    })
                    ->color(fn(string $state): string => match (strtolower($state)) {
                        'admin' => 'danger',      // Merah Tegas
                        'mahasiswa' => 'success', // Hijau Emerald
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->sortable(),

                // Kolom 5: Waktu Join Terdaftar
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar Pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Filter 1: Berdasarkan Peranan/Role Akses
                SelectFilter::make('role')
                    ->label('Role Akses')
                    ->options([
                        'admin' => 'Admin',
                        'mahasiswa' => 'Mahasiswa',
                    ])
                    ->native(false),

                // Filter 2: Berdasarkan Rentang Waktu Registrasi Akun
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('registered_from')
                            ->label('Terdaftar Dari')
                            ->native(false),
                        Forms\Components\DatePicker::make('registered_until')
                            ->label('Terdaftar Sampai')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['registered_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['registered_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                 
                        if ($data['registered_from'] ?? null) {
                            $indicators[] = 'Terdaftar dari: ' . \Carbon\Carbon::parse($data['registered_from'])->format('d M Y');
                        }
                 
                        if ($data['registered_until'] ?? null) {
                            $indicators[] = 'Terdaftar sampai: ' . \Carbon\Carbon::parse($data['registered_until'])->format('d M Y');
                        }
                 
                        return $indicators;
                    })
            ])
            ->actions([
                // Penyatuan Tombol Aksi ke Dropdown Menu Elipsis Vertikal yang Fleksibel
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->color('info'),
                    Tables\Actions\EditAction::make()->color('warning'),
                    Tables\Actions\DeleteAction::make()->color('danger'),
                ])->icon('heroicon-m-ellipsis-vertical')
                  ->tooltip('Aksi Data')
                  ->button()
                  ->label('Menu')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Arsip Master Data Pengguna')
                    ->description('Rincian otentikasi akun dan status biodata kemahasiswaan.')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                
                                // PANEL KIRI: Ringkasan Biodata Teknis (Kolom Besar)
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label('Nama Lengkap')
                                            ->weight('bold')
                                            ->color('primary'),

                                        TextEntry::make('role')
                                            ->label('Level Akses')
                                            ->badge()
                                            ->color(fn(string $state): string => match (strtolower($state)) {
                                                'admin' => 'danger',
                                                'mahasiswa' => 'success',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn(string $state): string => ucfirst($state)),

                                        TextEntry::make('nim')
                                            ->label('Nomor Induk Mahasiswa (NIM)')
                                            ->placeholder('-')
                                            ->visible(fn($record) => $record->role === 'mahasiswa')
                                            ->weight('medium'),

                                        TextEntry::make('email')
                                            ->label('Alamat Surat Elektronik (Email)'),

                                        TextEntry::make('nomor_hp')
                                            ->label('Nomor Telepon Seluler')
                                            ->placeholder('Belum ditautkan'),

                                        TextEntry::make('created_at')
                                            ->label('Waktu Registrasi Sistem')
                                            ->dateTime('d F Y - H:i \W\I\B'),
                                    ])->columnSpan(2),

                                // PANEL KANAN: Penempatan Avatar Center Bulat Besar
                                Grid::make(1)
                                    ->extraAttributes(['class' => 'flex justify-center justify-items-center text-center'])
                                    ->schema([
                                        ImageEntry::make('foto')
                                            ->label('Foto Profil Aktif')
                                            ->disk('public')
                                            ->circular()
                                            ->width(160)
                                            ->height(160)
                                            ->extraImgAttributes(['class' => 'shadow-md border-2 border-gray-100'])
                                            ->defaultImageUrl(fn($record) => 'https://ui-avatars.com/api/?background=6366f1&size=256&color=fff&name=' . urlencode($record->name))
                                            ->url(fn($record) => $record->foto ? asset('storage/' . $record->foto) : null)
                                            ->openUrlInNewTab(),
                                    ])->columnSpan(1),
                            ]),
                    ])
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}