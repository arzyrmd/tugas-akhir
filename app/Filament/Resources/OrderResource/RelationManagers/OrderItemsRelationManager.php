<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderItems';

    protected static ?string $recordTitleAttribute = 'id';
    protected static ?string $title = 'Produk yang Dipesan';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\ImageColumn::make('product.image')
                ->label('Gambar')
                ->circular()
                ->disk('public')
                ->height(80) // Increase the height (default is around 40)
                ->width(80), // Make it prop
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.category.name')
                    ->label('Kategori'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga Satuan')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Biasanya kita tidak ingin menambah item pesanan secara manual
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Biasanya kita tidak ingin mengedit/menghapus item pesanan secara manual
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
