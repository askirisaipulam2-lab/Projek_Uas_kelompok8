<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostCategoryResource\Pages;
use App\Models\PostCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostCategoryResource extends Resource
{
    protected static ?string $model = PostCategory::class;

    // --- PENGATURAN TAMPILAN SIDEBAR ---
    protected static ?string $navigationGroup = 'Blog';
    protected static ?string $navigationLabel = 'Kategori Post';
    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Kategori Post')
                            ->placeholder('Contoh: Laporan Kehilangan, Edukasi Keamanan')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(string $operation, $state, Forms\Set $set) =>
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug URL (Otomatis)')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->unique(PostCategory::class, 'slug', ignoreRecord: true),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->badge()
                    ->color('slate'),

                Tables\Columns\TextColumn::make('posts_count')
                    ->label('Jumlah Post')
                    ->counts('posts')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->icon('heroicon-m-ellipsis-vertical') // Ini adalah ikon titik tiga
                    ->tooltip('Aksi'),
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
            'index' => Pages\ListPostCategories::route('/'),
            'create' => Pages\CreatePostCategory::route('/create'),
            'edit' => Pages\EditPostCategory::route('/{record}/edit'),
        ];
    }
}