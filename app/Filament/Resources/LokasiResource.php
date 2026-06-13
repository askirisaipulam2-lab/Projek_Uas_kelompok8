<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LokasiResource\Pages;
use App\Filament\Resources\LokasiResource\RelationManagers;
use App\Models\Lokasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Filters\Filter;

class LokasiResource extends Resource
{
    protected static ?string $model = Lokasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Lokasi'; // Menggunakan kapital awal agar rapi di sidebar
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Sektor & Lokasi')
                    ->description('Daftarkan titik lokasi spesifik di area kampus untuk mempermudah pemetaan penemuan atau kehilangan barang.')
                    ->icon('heroicon-o-compass')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Ruangan / Titik Lokasi')
                            ->placeholder('Misal: Lapangan Futsal, Ruang Kelas 402, Kantin Belakang')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true), // Menghindari duplikasi penamaan lokasi

                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi Petunjuk Arus / Patokan Lokasi')
                            ->placeholder('Tambahkan catatan pembantu (misal: Lantai 2 gedung perpustakaan, sebelah kanan koridor utama)...')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom 1: Nama Lokasi Utama + Sub-Deskripsi di bawahnya (Sangat Hemat Tempat)
                Tables\Columns\TextColumn::make('nama')
                    ->label('Titik Lokasi')
                    ->weight('bold')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Lokasi $record): ?string => 
                        $record->deskripsi ? \Illuminate\Support\Str::limit($record->deskripsi, 60) : 'Tidak ada keterangan tambahan.'
                    ),

                // Kolom 2: Tanggal Masuk Registrasi Sektor
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y, H:i')
                    ->icon('heroicon-m-calendar')
                    ->iconColor('gray')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc') // Memastikan urutan data terbaru berada paling atas
            ->filters([
                // Filter berdasarkan Tanggal Lokasi Terdaftar di Sistem
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Terdaftar Dari')
                            ->native(false),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Terdaftar Sampai')
                            ->native(false),
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
                            $indicators[] = 'Terdaftar dari: ' . \Carbon\Carbon::parse($data['created_from'])->format('d M Y');
                        }
                 
                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Terdaftar sampai: ' . \Carbon\Carbon::parse($data['created_until'])->format('d M Y');
                        }
                 
                        return $indicators;
                    })
            ])
            ->actions([
                // Mengelompokkan aksi ke dropdown horizontal terpadu bertema "Menu"
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
                Section::make('Rincian Master Data Lokasi')
                    ->description('Detail penamaan sektor koordinat yang terdaftar di dalam sistem.')
                    ->icon('heroicon-o-map')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('nama')
                                    ->label('Nama Ruangan / Lokasi Kampus')
                                    ->weight('bold')
                                    ->color('primary')
                                    ->icon('heroicon-o-map-pin'),

                                TextEntry::make('created_at')
                                    ->label('Waktu Penambahan Sistem')
                                    ->dateTime('d F Y - H:i \W\I\B')
                                    ->icon('heroicon-o-clock')
                                    ->iconColor('gray'),
                            ]),

                        TextEntry::make('deskripsi')
                            ->label('Uraian / Petunjuk Tambahan Menuju Lokasi')
                            ->markdown()
                            ->prose()
                            ->columnSpanFull()
                            ->placeholder('Tidak ada rincian peta deskripsi khusus untuk lokasi ini.'),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLokasis::route('/'),
            'create' => Pages\CreateLokasi::route('/create'),
            'view' => Pages\ViewLokasi::route('/{record}'),
            'edit' => Pages\EditLokasi::route('/{record}/edit'),
        ];
    }
}