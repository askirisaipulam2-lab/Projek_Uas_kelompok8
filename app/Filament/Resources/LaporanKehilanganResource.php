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

class LaporanKehilanganResource extends Resource
{
    protected static ?string $model = LaporanKehilangan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                Forms\Components\DatePicker::make('tanggal_hilang')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'hilang' => 'Hilang',
                        'ditemukan' => 'Ditemukan',
                        'diklaim' => 'Diklaim',
                    ])
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
                Tables\Columns\TextColumn::make('tanggal_hilang')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanKehilangans::route('/'),
            'create' => Pages\CreateLaporanKehilangan::route('/create'),
            'edit' => Pages\EditLaporanKehilangan::route('/{record}/edit'),
        ];
    }
}
