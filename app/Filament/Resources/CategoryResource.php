<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Forms\Components\Wizard\Step;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Manajemen Produk';
    protected static ?string $navigationLabel = 'Kategori Produk';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Wizard::make([
                Step::make('Informasi Dasar')
                    ->description('Berikan nama dan slug untuk kategori')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $state, Forms\Set $set) =>
                                $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->readOnly()
                            ->dehydrated(),
                    ])
                    ->columns(2),

                Step::make('Media & Tampilan')
                    ->description('Upload gambar dan atur tampilan kategori')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Gambar Kategori')
                            ->image()
                            ->directory('categories')
                            ->deleteUploadedFileUsing(fn (Model $record) =>
                                $record->deleteImage()),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Kategori Unggulan')
                            ->default(false),
                    ]),

                Step::make('Pengaturan')
                    ->description('Atur urutan dan pengaturan lainnya')
                    ->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0)
                            ->helperText('Semakin kecil angka, semakin awal urutannya'),
                    ]),
            ])
            ->skippable() // ✅ Memungkinkan skip step dan klik langsung
            ->persistStepInQueryString() // ✅ Step tersimpan di URL
            ->columnSpanFull()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar')
                    ->height(60) // ✅ Perbesar gambar
                    ->width(60), // ✅ Perbesar gambar
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Filter Unggulan'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->tooltip('Aksi'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),

        ];
    }
}
