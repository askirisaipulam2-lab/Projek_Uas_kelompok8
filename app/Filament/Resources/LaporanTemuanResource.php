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

class LaporanTemuanResource extends Resource
{
    protected static ?string $model = LaporanTemuan::class;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationLabel = 'laporan temuan';

    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Pelapor')
                    ->relationship('user', 'name')
                    ->default(auth()->id())
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                Forms\Components\Select::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('lokasi_id')
                    ->label('Lokasi')
                    ->relationship('lokasi', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('deskripsi')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('tanggal_temuan')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'ditemukan' => 'Ditemukan',
                        'diklaim' => 'Diklaim',
                    ])
                    ->default('ditemukan')
                    ->required(),
                Forms\Components\FileUpload::make('gambar')
                    ->image()
                    ->disk('public')
                    ->directory('laporan-kehilangan')
                    ->fetchFileInformation(false)
                    ->openable()
                    ->downloadable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pelapor'),
                Tables\Columns\TextColumn::make('kategori.nama')
                    ->label('Kategori'),
                Tables\Columns\TextColumn::make('lokasi.nama')
                    ->label('Lokasi'),
                Tables\Columns\TextColumn::make('judul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_temuan')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\ImageColumn::make('gambar')
                    ->disk('public')
                    ->defaultImageUrl('https://placehold.co/100x100')
                    ->square()
                    ->size(80),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
        if (auth()->user()->role === 'admin') {
            return true;
        }

        return $record->user_id === auth()->id();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanTemuans::route('/'),
            'create' => Pages\CreateLaporanTemuan::route('/create'),
            'edit' => Pages\EditLaporanTemuan::route('/{record}/edit'),
        ];
    }
}
