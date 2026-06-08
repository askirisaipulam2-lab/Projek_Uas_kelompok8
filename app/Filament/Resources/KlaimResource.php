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

class KlaimResource extends Resource
{
    protected static ?string $model = Klaim::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationLabel = 'klaim';

    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('laporan_temuan_id')
                    ->relationship('laporanTemuan', 'judul')
                    ->label('Barang Temuan')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->default(auth()->id())
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                Forms\Components\Textarea::make('bukti_kepemilikan')
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ])
                    ->default('menunggu')
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('laporanTemuan.judul')
                    ->label('Barang Temuan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengklaim')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bukti_kepemilikan')
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\TextColumn::make('status')
                    ->sortable(),
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
            'edit' => Pages\EditKlaim::route('/{record}/edit'),
        ];
    }
}
