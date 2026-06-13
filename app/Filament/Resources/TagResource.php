<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use App\Filament\Resources\TagResource\RelationManagers;
use App\Models\Tag;
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

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'Tags'; // Diperbaiki menjadi kapital jamak agar rapi di sidebar
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Kelola Master Tag')
                    ->description('Tambahkan atau perbarui label penanda (tagging) untuk klasifikasi pencarian objek data.')
                    ->icon('heroicon-o-tag')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Tag Label')
                            ->placeholder('Misal: Elektronik, Dokumen, Berharga')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true) // Mencegah duplikasi nama tag yang sama
                            ->columnSpanFull(),
                    ])
                    ->compact() // Membuat padding section lebih ringkas & padat
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom 1: Nama Tag diubah menjadi Badge Elegant
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Tag')
                    ->badge()
                    ->color('primary') // Warna tema indigo/violet bawaan Filament yang modern
                    ->searchable()
                    ->sortable(),

                // Kolom 2: Tanggal Dibuat dengan format seragam
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y, H:i')
                    ->icon('heroicon-m-calendar')
                    ->iconColor('gray')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false), // Ditampilkan langsung karena kolomnya sedikit

                // Kolom 3: Tanggal Diperbarui
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc') // Arsip terbaru otomatis di baris paling atas
            ->filters([
                // Filter berdasarkan rentang tanggal pembuatan tag
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Dibuat Dari')
                            ->native(false),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Dibuat Sampai')
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
                            $indicators[] = 'Dibuat dari: ' . \Carbon\Carbon::parse($data['created_from'])->format('d M Y');
                        }
                 
                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Dibuat sampai: ' . \Carbon\Carbon::parse($data['created_until'])->format('d M Y');
                        }
                 
                        return $indicators;
                    })
            ])
            ->actions([
                // Penyatuan Tombol Aksi ke Dropdown Menu Elipsis Vertikal agar selaras
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
                Section::make('Rincian Arsip Label Tag')
                    ->description('Detail metadata klasifikasi sistem untuk kebutuhan filter database.')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('nama')
                                    ->label('Nama Tag Aktif')
                                    ->weight('bold')
                                    ->badge()
                                    ->color('primary'),

                                TextEntry::make('created_at')
                                    ->label('Waktu Registrasi Sistem')
                                    ->dateTime('d F Y - H:i \W\I\B')
                                    ->icon('heroicon-o-clock')
                                    ->iconColor('gray'),
                                
                                TextEntry::make('updated_at')
                                    ->label('Pembaruan Terakhir')
                                    ->dateTime('d F Y - H:i \W\I\B')
                                    ->icon('heroicon-o-arrow-path')
                                    ->iconColor('gray'),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'view' => Pages\ViewTag::route('/{record}'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}