<?php

namespace App\Filament\Resources\CustomProductRequestResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FinalProductRelationManager extends RelationManager
{
    protected static string $relationship = 'finalProduct';

    protected static ?string $title = 'Produk Final';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image_path')
                    ->label('Foto Produk Final')
                    ->image()
                    ->required()
                    ->directory('custom-product-finals')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('image_path')
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Foto Produk')
                    ->square()
                    ->size(100),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diunggah')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada foto produk final')
            ->emptyStateDescription('Upload foto produk final setelah pengerjaan selesai.')
            ->emptyStateIcon('heroicon-o-camera');
    }
}
