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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('kategori_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('lokasi_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('deskripsi')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('tanggal_temuan')
                    ->required(),
                Forms\Components\TextInput::make('status')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanTemuans::route('/'),
            'create' => Pages\CreateLaporanTemuan::route('/create'),
            'edit' => Pages\EditLaporanTemuan::route('/{record}/edit'),
        ];
    }
}
