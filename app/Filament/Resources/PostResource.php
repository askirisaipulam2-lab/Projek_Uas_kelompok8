<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationGroup = 'Blog';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Post Informasi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Mengubah tata letak menjadi Grid 3-kolom (Split Sidebar Layout) untuk UI yang lebih seimbang
                Forms\Components\Grid::make(3)
                    ->schema([

                        // Kolom Kiri (Mengambil porsi 2/3 halaman): Fokus Konten Utama
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make('Konten Utama')
                                    ->icon('heroicon-o-pencil-square')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Judul Informasi')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(string $operation, $state, Forms\Set $set) =>
                                                $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                                        Forms\Components\TextInput::make('slug')
                                            ->label('Slug URL')
                                            ->required()
                                            ->disabled()
                                            ->dehydrated()
                                            ->prefix('siberang.sttnf.ac.id/blog/'), // Visual indikator rute URL agar lebih keren

                                        Forms\Components\RichEditor::make('content')
                                            ->label('Isi Konten / Deskripsi')
                                            ->required()
                                            ->columnSpanFull(),
                                    ])->columns(1),
                            ])->columnSpan(['lg' => 2]),

                        // Kolom Kanan (Mengambil porsi 1/3 halaman): Fokus Metadata & Aset
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make('Pengaturan & Publikasi')
                                    ->icon('heroicon-o-cog')
                                    ->schema([
                                        Forms\Components\Select::make('category_id')
                                            ->label('Kategori Post')
                                            ->relationship('postCategory', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->native(false), // Dropdown modern bawaan Filament, bukan native browser

                                        Forms\Components\Toggle::make('is_published')
                                            ->label('Publikasikan ke Landing Page')
                                            ->default(true)
                                            ->onColor('success')
                                            ->offColor('danger'),
                                    ]),

                                Forms\Components\Section::make('Media')
                                    ->icon('heroicon-o-camera')
                                    ->schema([
                                        Forms\Components\FileUpload::make('image')
                                            ->label('Foto Barang / Ilustrasi')
                                            ->image()
                                            ->directory('posts')
                                            ->hiddenLabel(), // Menyembunyikan label ganda agar box upload lebih bersih
                                    ]),
                            ])->columnSpan(['lg' => 1]),

                    ])
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Foto')
                    ->circular(), // Mengubah kotak thumbnail menjadi lingkaran modern

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'), // Menebalkan teks judul untuk hirarki visual yang lebih baik

                Tables\Columns\TextColumn::make('postCategory.name')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn(string $state): string => match (strtolower($state)) {
                        'kehilangan' => 'danger',  // Merah
                        'temuan' => 'success', // Hijau
                        'edukasi' => 'info',    // Biru
                        default => 'warning',    // Abu-abu jika tidak cocok
                    })
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Status Publis')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc') // Mengurutkan data terbaru di paling atas secara default
            ->filters([])
            ->actions([
                Tables\Actions\ActionGroup::make([ // Membungkus aksi ke dalam drop-menu (titik tiga) agar baris tabel rapi
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->icon('heroicon-m-ellipsis-vertical')
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}