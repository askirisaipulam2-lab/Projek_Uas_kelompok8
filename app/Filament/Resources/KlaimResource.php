<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KlaimResource\Pages;
use App\Filament\Resources\KlaimResource\RelationManagers;
use App\Models\Klaim;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry; 
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;

class KlaimResource extends Resource
{
    protected static ?string $model = Klaim::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationLabel = 'Klaim Barang';
    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Menggunakan sistem Grid 3 kolom untuk membagi Form menjadi kiri (utama) dan kanan (media)
                Forms\Components\Grid::make(3)
                    ->schema([
                        
                        // SISI KIRI: Formulir Data (Mengambil 2 Kolom)
                        Forms\Components\Section::make('Data Pengajuan Klaim')
                            ->description('Tentukan barang yang diklaim dan isi bukti penjelasan dengan jelas.')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Select::make('laporan_temuan_id')
                                    ->relationship('laporanTemuan', 'judul')
                                    ->label('Barang Temuan')
                                    ->searchable()
                                    ->preload()
                                    ->loadingMessage('Memuat data barang...')
                                    ->required(),

                                Forms\Components\Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->label('Nama Pengklaim')
                                    ->default(auth()->id())
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),

                                Forms\Components\Textarea::make('bukti_kepemilikan')
                                    ->label('Deskripsi Bukti Kepemilikan')
                                    ->placeholder('Sebutkan ciri khusus, nomor seri, isi di dalam barang, lokasi jatuh, atau info validasi lainnya...')
                                    ->rows(5)
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(2),

                        // SISI KANAN: Status & Upload File (Mengambil 1 Kolom)
                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\Section::make('Status & Lampiran')
                                    ->icon('heroicon-o-photo')
                                    ->schema([
                                        Forms\Components\Select::make('status')
                                            ->options([
                                                'menunggu' => 'Menunggu',
                                                'disetujui' => 'Disetujui',
                                                'ditolak' => 'Ditolak',
                                            ])
                                            ->default('menunggu')
                                            ->selectablePlaceholder(false)
                                            ->native(false) // Membuat UI select dropdown melayang keren ala Tailwind
                                            ->required(),

                                        Forms\Components\FileUpload::make('foto_bukti')
                                            ->label('Foto Bukti Pendukung')
                                            ->image()
                                            ->imageEditor() // Mengaktifkan fitur crop/potong gambar langsung di browser
                                            ->directory('klaim-bukti')
                                            ->maxSize(2048)
                                            ->downloadable()
                                            ->openable()
                                            ->placeholder('Klik / Tarik foto ke sini'),
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
                // Kolom 1: Foto Bukti dengan tampilan Square Rounded yang Elegan
                Tables\Columns\ImageColumn::make('foto_bukti')
                    ->label('Foto Bukti')
                    ->square()
                    ->stacked() 
                    ->height(50)
                    ->width(50)
                    ->extraImgAttributes(['class' => 'rounded-xl shadow-sm border border-gray-200']) 
                    ->defaultImageUrl(url('images/placeholder.png')),

                // Kolom 2: Informasi Barang Temuan
                Tables\Columns\TextColumn::make('laporanTemuan.judul')
                    ->label('Barang Temuan')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                // Kolom 3: Pengklaim + Ada sub-informasi tanggal di bawah namanya
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Data Pengklaim')
                    ->description(fn (Klaim $record): string => 'Diajukan: ' . $record->created_at->diffForHumans()) 
                    ->searchable()
                    ->sortable(),

                // Kolom 4: Potongan Deskripsi Bukti Kepemilikan
                Tables\Columns\TextColumn::make('bukti_kepemilikan')
                    ->label('Detail Deskripsi')
                    ->limit(40)
                    ->color('gray')
                    ->wrap(),

                // Kolom 5: Status Badge Berwarna + Ikon Indikator Dinamis
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->icon(fn (string $state): string => match ($state) {
                        'menunggu' => 'heroicon-m-clock',
                        'disetujui' => 'heroicon-m-check-circle',
                        'ditolak' => 'heroicon-m-x-circle',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            // --- BAGIAN FILTERS YANG SUDAH DITAMBAHKAN ---
            ->filters([
                // Filter 1: Berdasarkan Status Pengajuan Klaim
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Klaim')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ])
                    ->native(false),

                // Filter 2: Berdasarkan Rentang Tanggal Pengajuan Masuk ke Sistem
                Tables\Filters\Filter::make('created_at')
                    ->label('Tanggal Pengajuan')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Dari Tanggal')
                            ->placeholder('Pilih tanggal awal'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Sampai Tanggal')
                            ->placeholder('Pilih tanggal akhir'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators[] = 'Diajukan dari: ' . \Carbon\Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Diajukan sampai: ' . \Carbon\Carbon::parse($data['created_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
            ])
            // ---------------------------------------------
            ->actions([
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
                Section::make('Review Berkas Klaim Transaksi')
                    ->description('Halaman pengecekan data kecocokan barang temuan dengan bukti yang dibawa user.')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        
                        // Menggunakan Tabs untuk memisahkan Detail Informasi Teknis vs File Foto Media
                        Tabs::make('Detail Ringkasan')
                            ->tabs([
                                
                                // Tab 1: Informasi Formulir
                                Tabs\Tab::make('Detail Informasi')
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('laporanTemuan.judul')
                                                    ->label('Barang Temuan yang Diklaim')
                                                    ->weight('bold')
                                                    ->color('primary'),
                                                
                                                TextEntry::make('status')
                                                    ->badge()
                                                    ->color(fn(string $state): string => match ($state) {
                                                        'menunggu' => 'warning',
                                                        'disetujui' => 'success',
                                                        'ditolak' => 'danger',
                                                        default => 'gray',
                                                    }),

                                                TextEntry::make('user.name')
                                                    ->label('Diajukan Oleh (Mahasiswa)'),
                                                
                                                TextEntry::make('created_at')
                                                    ->label('Waktu Cetak Masuk System')
                                                    ->dateTime('d F Y - H:i WIB'),
                                            ]),

                                        TextEntry::make('bukti_kepemilikan')
                                            ->label('Pernyataan Bukti Kepemilikan')
                                            ->markdown()
                                            ->prose() 
                                            ->columnSpanFull(),
                                    ]),

                                // Tab 2: Khusus Melihat Foto Ukuran Penuh
                                Tabs\Tab::make('Lampiran Gambar Pendukung')
                                    ->icon('heroicon-o-camera')
                                    ->schema([
                                        ImageEntry::make('foto_bukti')
                                            ->label('Preview Foto Fisik Barang Bukti')
                                            ->height(280) 
                                            ->extraImgAttributes(['class' => 'rounded-2xl shadow-md border border-gray-100'])
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
            'bukti_kepemilikan',
            'laporanTemuan.judul',
            'user.name',
        ];
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->laporanTemuan?->judul ?? 'Klaim';
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Pengklaim' => $record->user?->name,
            'Status' => $record->status,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->role === 'mahasiswa') {
            return $query->where('user_id', auth()->id());
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKlaims::route('/'),
            'create' => Pages\CreateKlaim::route('/create'),
            'view' => Pages\ViewKlaim::route('/{record}'),
            'edit' => Pages\EditKlaim::route('/{record}/edit'),
        ];
    }
}