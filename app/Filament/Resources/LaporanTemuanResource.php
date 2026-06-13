<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanTemuanResource\Pages;
use App\Filament\Resources\LaporanTemuanResource\RelationManagers;
use App\Models\LaporanTemuan;
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
use Filament\Infolists\Components\Tabs;
use Filament\Tables\Filters\SelectFilter;

class LaporanTemuanResource extends Resource
{
    protected static ?string $model = LaporanTemuan::class;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationLabel = 'Laporan Temuan';
    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        
                        // SISI KIRI: Formulir Utama (Mengambil 2 Kolom)
                        Forms\Components\Section::make('Detail Temuan Barang')
                            ->description('Catat informasi barang hilang yang berhasil ditemukan di lingkungan kampus.')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label('Pelapor / Penemu')
                                    ->relationship('user', 'name')
                                    ->default(auth()->id())
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),

                                Forms\Components\TextInput::make('judul')
                                    ->label('Nama Barang Temuan')
                                    ->placeholder('Misal: Tumblr Starbuck Hitam, Kunci Motor')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Select::make('kategori_id')
                                    ->label('Kategori Barang')
                                    ->relationship('kategori', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->required(),

                                Forms\Components\Select::make('lokasi_id')
                                    ->label('Titik Lokasi Ditemukan')
                                    ->relationship('lokasi', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->required(),

                                Forms\Components\DatePicker::make('tanggal_temuan')
                                    ->label('Tanggal Ditemukan')
                                    ->native(false)
                                    ->maxDate(now()) // Memastikan tidak bisa memilih tanggal masa depan
                                    ->required(),

                                Forms\Components\Select::make('status')
                                    ->options([
                                        'ditemukan' => 'Ditemukan',
                                        'diklaim' => 'Diklaim',
                                    ])
                                    ->default('ditemukan')
                                    ->native(false)
                                    ->selectablePlaceholder(false)
                                    ->required(),

                                Forms\Components\Textarea::make('deskripsi')
                                    ->label('Kondisi Fisik / Keterangan Tambahan')
                                    ->placeholder('Sebutkan kondisi barang saat ditemukan, atau instruksi tempat penyimpanan sementara (misal: Dititipkan di FO/SATPAM)...')
                                    ->rows(5)
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpan(2),

                        // SISI KANAN: Lampiran Foto (Mengambil 1 Kolom)
                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\Section::make('Visual Barang')
                                    ->description('Unggah foto barang temuan untuk mempermudah identifikasi oleh pemilik asli.')
                                    ->icon('heroicon-o-camera')
                                    ->schema([
                                        Forms\Components\FileUpload::make('gambar')
                                            ->label('Foto Barang')
                                            ->image()
                                            ->imageEditor() // Memungkinkan crop & rotasi gambar langsung di dashboard
                                            ->disk('public')
                                            ->directory('laporan-temuan') // Memisahkan folder agar rapi di storage
                                            ->fetchFileInformation(false)
                                            ->openable()
                                            ->downloadable()
                                            ->placeholder('Klik / Seret foto ke sini'),
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
                // Kolom 1: Pratinjau Gambar Rounded Elegant Box
                Tables\Columns\ImageColumn::make('gambar')
                    ->label('Foto')
                    ->disk('public')
                    ->square()
                    ->height(55)
                    ->width(55)
                    ->extraImgAttributes(['class' => 'rounded-xl shadow-sm border border-gray-100'])
                    ->defaultImageUrl('https://placehold.co/100x100'),

                // Kolom 2: Nama Barang + Sub-Deskripsi Ringkasan Atribut Data
                Tables\Columns\TextColumn::make('judul')
                    ->label('Detail Laporan Temuan')
                    ->weight('bold')
                    ->searchable()
                    ->description(fn (LaporanTemuan $record): string => 
                        "Kategori: " . ($record->kategori?->nama ?? '-') . 
                        " | Lokasi: " . ($record->lokasi?->nama ?? '-') . 
                        " (" . ($record->tanggal_temuan ? \Carbon\Carbon::parse($record->tanggal_temuan)->format('d M Y') : '-') . ")"
                    ),

                // Kolom 3: Nama Penemu
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Penemu / Pelapor')
                    ->icon('heroicon-m-user')
                    ->iconColor('gray')
                    ->searchable()
                    ->sortable(),

                // Kolom 4: Badge Status Berwarna & Berikon Selaras
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->icon(fn (string $state): string => match (strtolower($state)) {
                        'ditemukan' => 'heroicon-m-arrow-path',
                        'diklaim' => 'heroicon-m-check-badge',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->color(fn(string $state): string => match (strtolower($state)) {
                        'ditemukan' => 'warning', // Kuning Oranye
                        'diklaim' => 'success',   // Hijau Emerald
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Masuk')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Filter 1: Berdasarkan Status Temuan
                SelectFilter::make('status')
                    ->label('Status Temuan')
                    ->options([
                        'ditemukan' => 'Ditemukan',
                        'diklaim' => 'Diklaim',
                    ])
                    ->native(false),

                // Filter 2: Berdasarkan Kategori Barang (Relasi Dinamis)
                SelectFilter::make('kategori_id')
                    ->label('Kategori Barang')
                    ->relationship('kategori', 'nama')
                    ->searchable()
                    ->preload()
                    ->native(false),

                // Filter 3: Berdasarkan Lokasi (Relasi Dinamis)
                SelectFilter::make('lokasi_id')
                    ->label('Lokasi Penemuan')
                    ->relationship('lokasi', 'nama')
                    ->searchable()
                    ->preload()
                    ->native(false),
            ])
            ->actions([
                // Mengelompokkan tombol aksi ke dalam dropdown vertikal titik tiga yang bersih
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
                Section::make('Lembar Verifikasi Temuan Barang')
                    ->description('Detail arsip penemuan sebagai acuan pembanding ketika ada user yang mengajukan berkas klaim kepemilikan.')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        
                        Tabs::make('Arsip Menu')
                            ->tabs([
                                
                                // TAB 1: Rincian Data Teknis
                                Tabs\Tab::make('Data Temuan & Lokasi')
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('judul')
                                                    ->label('Nama Barang Temuan')
                                                    ->weight('bold')
                                                    ->color('primary'),

                                                TextEntry::make('status')
                                                    ->badge()
                                                    ->color(fn(string $state): string => match (strtolower($state)) {
                                                        'ditemukan' => 'warning',
                                                        'diklaim' => 'success',
                                                        default => 'gray',
                                                    })
                                                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),

                                                TextEntry::make('user.name')
                                                    ->label('Nama Penemu (Staf/Mahasiswa)'),

                                                TextEntry::make('kategori.nama')
                                                    ->label('Kategori Kelompok Barang'),

                                                TextEntry::make('lokasi.nama')
                                                    ->label('Titik Sektor Penemuan'),

                                                TextEntry::make('tanggal_temuan')
                                                    ->label('Tanggal Penemuan Barang')
                                                    ->date('l, d F Y'),

                                                TextEntry::make('created_at')
                                                    ->label('Waktu Masuk Sistem')
                                                    ->dateTime('d F Y - H:i WIB'),
                                            ]),

                                        TextEntry::make('deskripsi')
                                            ->label('Uraian Kondisi & Keterangan Penyimpanan')
                                            ->markdown()
                                            ->prose()
                                            ->columnSpanFull(),
                                    ]),

                                // TAB 2: Lampiran Foto Resolusi Besar
                                Tabs\Tab::make('Dokumentasi Foto Fisik')
                                    ->icon('heroicon-o-camera')
                                    ->schema([
                                        ImageEntry::make('gambar')
                                            ->label('Pratinjau Visual Barang Bukti Temuan')
                                            ->disk('public')
                                            ->height(280)
                                            ->extraImgAttributes(['class' => 'rounded-2xl shadow-md border border-gray-100'])
                                            ->defaultImageUrl('https://placehold.co/300x300')
                                            ->url(fn($record) => $record->gambar ? asset('storage/' . $record->gambar) : null)
                                            ->openUrlInNewTab()
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canEdit($record): bool
    {
        if (!auth()->check()) {
            return false;
        }

        if (auth()->user()->role === 'admin') {
            return true;
        }

        return $record->user_id === auth()->id();
    }

    public static function canDelete($record): bool
    {
        if (!auth()->check()) {
            return false;
        }

        if (auth()->user()->role === 'admin') {
            return true;
        }

        return $record->user_id === auth()->id();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'judul',
            'deskripsi',
            'kategori.nama',
            'lokasi.nama',
        ];
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->judul;
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Kategori' => $record->kategori?->nama,
            'Lokasi' => $record->lokasi?->nama,
            'Status' => $record->status,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanTemuans::route('/'),
            'create' => Pages\CreateLaporanTemuan::route('/create'),
            'view' => Pages\ViewLaporanTemuan::route('/{record}'),
            'edit' => Pages\EditLaporanTemuan::route('/{record}/edit'),
        ];
    }
}