<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotifikasiResource\Pages;
use App\Models\Notifikasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NotifikasiResource extends Resource
{
    protected static ?string $model = Notifikasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationLabel = 'Notifikasi';

    protected static ?string $navigationGroup = 'Sistem';

    public static function getNavigationBadge(): ?string
    {
        return (string) Notifikasi::where('is_read', false)->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('pesan')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_read')
                    ->label('Sudah Dibaca')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('judul')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pesan')
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\IconColumn::make('is_read')
                    ->label('Dibaca')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d M Y H:i')
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
            'index' => Pages\ListNotifikasis::route('/'),
            'create' => Pages\CreateNotifikasi::route('/create'),
            'edit' => Pages\EditNotifikasi::route('/{record}/edit'),
        ];
    }
}