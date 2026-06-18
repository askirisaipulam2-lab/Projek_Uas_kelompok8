<?php

namespace App\Filament\Resources;

// Memastikan semua komponen halaman internal ter-import dengan benar
use App\Filament\Resources\NotifikasiResource\Pages\CreateNotifikasi;
use App\Filament\Resources\NotifikasiResource\Pages\EditNotifikasi;
use App\Filament\Resources\NotifikasiResource\Pages\ListNotifikasis;
use App\Models\Notifikasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\IconEntry;

class NotifikasiResource extends Resource
{
    protected static ?string $model = Notifikasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationLabel = 'Notifikasi';
    protected static ?string $navigationGroup = 'Sistem';

    // Badge navigasi otomatis menghitung jumlah notifikasi yang belum dibaca sesuai hak akses user log-in
    public static function getNavigationBadge(): ?string
    {
        $query = Notifikasi::where('is_read', false);

        if (auth()->user()?->role === 'mahasiswa') {
            $query->where('user_id', auth()->id());
        }

        return (string) $query->count();
    }

    // Mengubah warna badge menjadi merah (danger) jika ada pesan penting belum terbaca
    protected static ?string $navigationBadgeColor = 'danger';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Buat & Siarkan Pemberitahuan')
                    ->description('Kirim pesan notifikasi sistem secara terarah kepada pengguna tertentu.')
                    ->icon('heroicon-o-megaphone')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Target Pengguna (User)')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required(),

                        Forms\Components\TextInput::make('judul')
                            ->label('Subjek / Judul Notifikasi')
                            ->placeholder('Misal: Validasi Berkas Berhasil, Barang Ditemukan')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('pesan')
                            ->label('Isi Pesan Konteks')
                            ->placeholder('Tuliskan detail informasi pesan secara jelas di sini...')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_read')
                            ->label('Tandai Langsung Sebagai Sudah Dibaca')
                            ->default(false)
                            ->inline(false)
                            ->onIcon('heroicon-m-eye')
                            ->offIcon('heroicon-m-eye-slash'),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom 1: Informasi Target User
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Penerima')
                    ->weight('bold')
                    ->icon('heroicon-o-user')
                    ->iconColor('primary')
                    ->searchable()
                    ->sortable(),

                // Kolom 2: Judul + Potongan Pesan di bawahnya (Sangat Indah & Ringkas)
                Tables\Columns\TextColumn::make('judul')
                    ->label('Konteks Notifikasi')
                    ->weight('medium')
                    ->searchable()
                    ->sortable()
                    ->description(
                        fn(Notifikasi $record): string =>
                        \Illuminate\Support\Str::limit($record->pesan, 60)
                    ),

                // Kolom 3: STATUS INTERAKTIF (Bisa diklik langsung di tabel & otomatis tersimpan ke DB)
                Tables\Columns\ToggleColumn::make('is_read')
                    ->label('Status Baca')
                    ->tooltip('Klik untuk mengubah status baca secara instan'),

                // Kolom 4: Tanggal Masuk Pemberitahuan
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Kirim')
                    ->dateTime('d M Y, H:i')
                    ->icon('heroicon-o-clock')
                    ->iconColor('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc') // Pesan terbaru selalu berada paling atas
            ->filters([
                // Filter cepat berdasarkan status baca di tabel
                Tables\Filters\SelectFilter::make('is_read')
                    ->label('Status Informasi')
                    ->options([
                        '1' => 'Sudah Dibaca',
                        '0' => 'Belum Dibaca',
                    ])
                    ->native(false),
            ])
            ->actions([
                // Memadatkan aksi ke dalam satu tombol Menu Dropdown premium
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->color('info'),
                    Tables\Actions\EditAction::make()->color('warning'),
                    Tables\Actions\DeleteAction::make()->color('danger'),
                ])->icon('heroicon-m-ellipsis-vertical')
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
                InfoSection::make('Arsip Lembar Notifikasi')
                    ->description('Detail riwayat pengiriman pesan log sistem.')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        InfoGrid::make(3)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Penerima Pesan')
                                    ->weight('bold')
                                    ->color('primary'),

                                TextEntry::make('created_at')
                                    ->label('Dikirim Pada')
                                    ->dateTime('d F Y - H:i \W\I\B'),

                                IconEntry::make('is_read')
                                    ->label('Status Konfirmasi Baca')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-envelope-open')
                                    ->falseIcon('heroicon-o-envelope')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                            ]),

                        TextEntry::make('judul')
                            ->label('Subjek Maklumat')
                            ->weight('bold')
                            ->extraAttributes(['class' => 'border-t pt-4 mt-2']),

                        TextEntry::make('pesan')
                            ->label('Uraian / Isi Pesan Dokumen')
                            ->prose()
                            ->markdown()
                            ->columnSpanFull()
                            ->placeholder('Tidak ada rincian pesan tertulis.'),
                    ])
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    // Mengunci scope query data: Mahasiswa hanya melihat pesan miliknya, Admin menguasai semua data
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotifikasis::route('/'),
            'create' => CreateNotifikasi::route('/create'),
            // 'view' => ViewNotifikasi::route('/{record}'), // <-- SUDAH AKTIF SEMPURNA
            'edit' => EditNotifikasi::route('/{record}/edit'),
        ];
    }
}