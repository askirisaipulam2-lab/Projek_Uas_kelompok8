<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KatagoriResource\Pages;
use App\Filament\Resources\KatagoriResource\RelationManagers;
use App\Models\Kategori;
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

class KatagoriResource extends Resource
{
    protected static ?string $model = Kategori::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationLabel = 'Kategori'; 
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Manajemen Kategori Objek')
                    ->description('Kelola kelompok atau klasifikasi utama barang untuk memudahkan strukturisasi inventarisasi sistem.')
                    ->icon('heroicon-o-folder-open')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Kategori')
                            ->placeholder('Misal: Elektronik, Fasilitas Kampus, Dokumen Resmi')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true), 

                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi Ringkas Kategori')
                            ->placeholder('Tambahkan penjelasan cakupan barang untuk kategori ini...')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom 1: Nama Kategori Utama + Sub-Deskripsi di bawahnya
                Tables\Columns\TextColumn::make('nama')
                    ->label('Klasifikasi Kategori')
                    ->weight('bold')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Kategori $record): ?string => 
                        $record->deskripsi ? \Illuminate\Support\Str::limit($record->deskripsi, 65) : 'Tidak ada deskripsi tambahan.'
                    ),

                // Kolom 2: Waktu Registrasi Kategori
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
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
            ->defaultSort('created_at', 'desc') 
            ->filters([
                // Filter 1: Menyaring berdasarkan kelengkapan deskripsi
                Tables\Filters\TernaryFilter::make('has_deskripsi')
                    ->label('Kelengkapan Deskripsi')
                    ->placeholder('Semua Data')
                    ->trueLabel('Memiliki Deskripsi')
                    ->falseLabel('Tanpa Deskripsi')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('deskripsi')->where('deskripsi', '!=', ''),
                        false: fn (Builder $query) => $query->whereNull('deskripsi')->orWhere('deskripsi', ''),
                    )
                    ->native(false),

                // Filter 2: Menyaring berdasarkan rentang tanggal pembuatan data master
                Tables\Filters\Filter::make('created_at')
                    ->label('Tanggal Registrasi')
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
                            $indicators[] = 'Dibuat dari: ' . \Carbon\Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Dibuat sampai: ' . \Carbon\Carbon::parse($data['created_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
            ])
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
                Section::make('Arsip Detail Klasifikasi Kategori')
                    ->description('Rincian metadata pengelompokan sistem data master.')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('nama')
                                    ->label('Nama Kategori Aktif')
                                    ->weight('bold')
                                    ->color('primary')
                                    ->icon('heroicon-o-folder'),

                                TextEntry::make('created_at')
                                    ->label('Waktu Penambahan Sistem')
                                    ->dateTime('d F Y - H:i \W\I\B')
                                    ->icon('heroicon-o-clock')
                                    ->iconColor('gray'),
                            ]),

                        TextEntry::make('deskripsi')
                            ->label('Uraian Ruang Lingkup Kategori')
                            ->markdown()
                            ->prose() 
                            ->columnSpanFull()
                            ->placeholder('Tidak ada catatan deskripsi khusus mengenai kategori ini.'),
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
            'index' => Pages\ListKatagoris::route('/'),
            'create' => Pages\CreateKatagori::route('/create'),
            'view' => Pages\ViewKatagori::route('/{record}'),
            'edit' => Pages\EditKatagori::route('/{record}/edit'),
        ];
    }
}