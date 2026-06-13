<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanKehilanganResource\Pages;
use App\Filament\Resources\LaporanKehilanganResource\RelationManagers;
use App\Models\LaporanKehilangan;
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

class LaporanKehilanganResource extends Resource
{
    protected static ?string $model = LaporanKehilangan::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-circle';
    protected static ?string $navigationLabel = 'Laporan Kehilangan';
    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        
                        // SISI KIRI: Formulir Informasi Kronologi (Mengambil 2 Kolom)
                        Forms\Components\Section::make('Informasi Utama Kehilangan')
                            ->description('Tuliskan detail barang yang hilang beserta lokasi terakhir kali Anda mengingatnya.')
                            ->icon('heroicon-o-document-magnifying-glass')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label('Pelapor')
                                    ->relationship('user', 'name')
                                    ->default(auth()->id())
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),

                                Forms\Components\TextInput::make('judul')
                                    ->label('Nama Barang / Judul Laporan')
                                    ->placeholder('Misal: Kunci Motor Honda Beat, Dompet Kulit Hitam')
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
                                    ->label('Perkiraan Lokasi Hilang')
                                    ->relationship('lokasi', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->required(),

                                Forms\Components\DatePicker::make('tanggal_hilang')
                                    ->label('Tanggal Kejadian')
                                    ->native(false) // Memunculkan datepicker pop-up bawaan Tailwind yang sleek
                                    ->maxDate(now()) // Memblokir pemilihan tanggal masa depan
                                    ->required(),

                                Forms\Components\Select::make('status')
                                    ->options([
                                        'hilang' => 'Hilang',
                                        'ditemukan' => 'Ditemukan',
                                        'diklaim' => 'Diklaim',
                                    ])
                                    ->default('hilang')
                                    ->native(false)
                                    ->selectablePlaceholder(false)
                                    ->required(),

                                Forms\Components\Select::make('tags')
                                    ->label('Tags Kendala / Atribut Warna')
                                    ->relationship('tags', 'nama')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('deskripsi')
                                    ->label('Kronologi / Karakteristik Spesifik Barang')
                                    ->placeholder('Sebutkan gantungan kuncinya, stiker yang menempel, warna case, isi di dalam tas, atau runtutan kronologinya...')
                                    ->rows(5)
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpan(2),

                        // SISI KANAN: Lampiran Media Gambar (Mengambil 1 Kolom)
                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\Section::make('Lampiran Pendukung')
                                    ->description('Unggah dokumentasi bentuk fisik barang sebelum hilang jika ada.')
                                    ->icon('heroicon-o-camera')
                                    ->schema([
                                        Forms\Components\FileUpload::make('gambar')
                                            ->label('Foto Barang')
                                            ->image()
                                            ->imageEditor() // Fitur crop & rotate langsung di browser sebelum upload!
                                            ->disk('public')
                                            ->directory('laporan-kehilangan')
                                            ->fetchFileInformation(false)
                                            ->openable()
                                            ->downloadable()
                                            ->placeholder('Klik atau seret foto ke sini'),
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
                // Kolom 1: Foto Barang dengan bentuk Square Rounded Box Premium
                Tables\Columns\ImageColumn::make('gambar')
                    ->label('Foto')
                    ->disk('public')
                    ->square()
                    ->height(55)
                    ->width(55)
                    ->extraImgAttributes(['class' => 'rounded-xl shadow-sm border border-gray-100'])
                    ->defaultImageUrl('https://placehold.co/100x100'),

                // Kolom 2: Judul Laporan + Sub-Informasi Kategori, Lokasi & Waktu di bawahnya! (Sangat Hemat Ruang)
                Tables\Columns\TextColumn::make('judul')
                    ->label('Detail Laporan Kehilangan')
                    ->weight('bold')
                    ->searchable()
                    ->description(fn (LaporanKehilangan $record): string => 
                        "Kategori: " . ($record->kategori?->nama ?? '-') . 
                        " | Lokasi: " . ($record->lokasi?->nama ?? '-') . 
                        " (" . ($record->tanggal_hilang ? \Carbon\Carbon::parse($record->tanggal_hilang)->format('d M Y') : '-') . ")"
                    ),

                // Kolom 3: Identitas Pelapor Mahasiswa
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pelapor')
                    ->icon('heroicon-m-user')
                    ->iconColor('gray')
                    ->searchable()
                    ->sortable(),

                // Kolom 4: Status Badge dengan Ikon Indikator Selaras
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->icon(fn (string $state): string => match (strtolower($state)) {
                        'hilang' => 'heroicon-m-exclamation-triangle',
                        'ditemukan' => 'heroicon-m-arrow-path',
                        'diklaim' => 'heroicon-m-check-badge',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'hilang' => 'danger',     // Merah
                        'ditemukan' => 'warning', // Kuning/Oranye
                        'diklaim' => 'success',   // Hijau Emerald
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->sortable(),

                // Kolom 5: Tags Atribut
                Tables\Columns\TextColumn::make('tags.nama')
                    ->badge()
                    ->color('info')
                    ->separator(',')
                    ->label('Tags')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Masuk')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            // --- BAGIAN MENU FILTERS ---
            ->filters([
                // Filter 1: Berdasarkan Status Barang
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Laporan')
                    ->options([
                        'hilang' => 'Hilang',
                        'ditemukan' => 'Ditemukan',
                        'diklaim' => 'Diklaim',
                    ])
                    ->native(false),

                // Filter 2: Berdasarkan Kategori Barang (Relasional)
                Tables\Filters\SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama')
                    ->searchable()
                    ->preload()
                    ->native(false),

                // Filter 3: Berdasarkan Lokasi Kejadian (Relasional)
                Tables\Filters\SelectFilter::make('lokasi_id')
                    ->label('Lokasi Kejadian')
                    ->relationship('lokasi', 'nama')
                    ->searchable()
                    ->preload()
                    ->native(false),

                // Filter 4: Berdasarkan Rentang Tanggal Kejadian Kehilangan
                Tables\Filters\Filter::make('tanggal_hilang')
                    ->label('Tanggal Kejadian')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal')
                            ->native(false)
                            ->placeholder('Pilih tanggal awal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal')
                            ->native(false)
                            ->placeholder('Pilih tanggal akhir'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_hilang', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_hilang', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari_tanggal'] ?? null) {
                            $indicators[] = 'Kehilangan dari: ' . \Carbon\Carbon::parse($data['dari_tanggal'])->toFormattedDateString();
                        }
                        if ($data['sampai_tanggal'] ?? null) {
                            $indicators[] = 'Kehilangan sampai: ' . \Carbon\Carbon::parse($data['sampai_tanggal'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
            ])
            // ---------------------------
            ->actions([
                // Penyatuan Tombol ke Dropdown Menu Elipsis agar Elegan
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
                Section::make('Berkas Rincian Kronologi Kehilangan')
                    ->description('Lembar verifikasi pencarian untuk mencocokkan data kehilangan mahasiswa dengan barang temuan di lapangan.')
                    ->icon('heroicon-o-archive-box-arrow-down')
                    ->schema([
                        
                        Tabs::make('Review Menu')
                            ->tabs([
                                
                                // TAB 1: Rincian Kronologi
                                Tabs\Tab::make('Data Kronologi & Identitas')
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('judul')
                                                    ->label('Nama Barang Hilang')
                                                    ->weight('bold')
                                                    ->color('primary'),

                                                TextEntry::make('status')
                                                    ->badge()
                                                    ->color(fn(string $state): string => match (strtolower($state)) {
                                                        'hilang' => 'danger',
                                                        'ditemukan' => 'warning',
                                                        'diklaim' => 'success',
                                                        default => 'gray',
                                                    })
                                                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),

                                                TextEntry::make('user.name')
                                                    ->label('Nama Pelapor (Mahasiswa)'),

                                                TextEntry::make('kategori.nama')
                                                    ->label('Kategori Kelompok Barang'),

                                                TextEntry::make('lokasi.nama')
                                                    ->label('Titik Sektor Kehilangan'),

                                                TextEntry::make('tanggal_hilang')
                                                    ->label('Tanggal Kejadian Kehilangan')
                                                    ->date('l, d F Y'),

                                                TextEntry::make('tags.nama')
                                                    ->label('Karakteristik Tags')
                                                    ->badge()
                                                    ->color('info'),

                                                TextEntry::make('created_at')
                                                    ->label('Waktu Submit Laporan')
                                                    ->dateTime('d F Y - H:i WIB'),
                                            ]),

                                        TextEntry::make('deskripsi')
                                            ->label('Uraian Lengkap Kronologi Kejadian')
                                            ->markdown()
                                            ->prose()
                                            ->columnSpanFull(),
                                    ]),

                                // TAB 2: Lampiran Dokumentasi Gambar
                                Tabs\Tab::make('Dokumentasi Foto Fisik')
                                    ->icon('heroicon-o-camera')
                                    ->schema([
                                        ImageEntry::make('gambar')
                                            ->label('Preview Visual Foto Barang')
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

    public static function getEloquentQuery(): Builder
    {
        if (!auth()->check()) {
            return parent::getEloquentQuery();
        }

        $query = parent::getEloquentQuery();

        if (auth()->user()->role === 'mahasiswa') {
            return $query->where('user_id', auth()->id());
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanKehilangans::route('/'),
            'create' => Pages\CreateLaporanKehilangan::route('/create'),
            'view' => Pages\ViewLaporanKehilangan::route('/{record}'),
            'edit' => Pages\EditLaporanKehilangan::route('/{record}/edit'),
        ];
    }
}