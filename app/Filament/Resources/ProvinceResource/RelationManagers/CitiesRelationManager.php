<?php

namespace App\Filament\Resources\ProvinceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'cities';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Kota';

    protected static ?string $modelLabel = 'Kota';

    protected static ?string $pluralModelLabel = 'Kota-kota';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Kota')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->label('Kode Kota')
                    ->required()
                    ->maxLength(5)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('shipping_cost')
                    ->label('Biaya Dasar Pengiriman')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('Rp')
                    ->suffixIcon('heroicon-o-truck'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kota')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode Kota')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipping_cost')
                    ->label('Biaya Pengiriman')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Kota Baru'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
