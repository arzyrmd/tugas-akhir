<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Grid::make(2)->schema([
                // Basic Information Section
                Section::make('Informasi Dasar')
                    ->schema([
                        ImageEntry::make('image')
                            ->label('Gambar Utama')
                            ->disk('public')
                            ->height(300)
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nama Produk')
                                    ->inlineLabel(),

                                TextEntry::make('slug')
                                    ->label('Slug')
                                    ->inlineLabel(),

                                TextEntry::make('category.name')
                                    ->label('Kategori')
                                    ->inlineLabel(),

                                TextEntry::make('price')
                                    ->label('Harga')
                                    ->money('idr')
                                    ->inlineLabel(),

                                TextEntry::make('stock')
                                    ->label('Stok')
                                    ->inlineLabel(),

                                TextEntry::make('material')
                                    ->label('Material')
                                    ->inlineLabel(),

                                IconEntry::make('is_featured')
                                    ->label('Produk Unggulan')
                                    ->boolean()
                                    ->inlineLabel(),

                                IconEntry::make('is_active')
                                    ->label('Aktif')
                                    ->boolean()
                                    ->inlineLabel(),
                            ])
                            ->columnSpanFull(),
                    ]),

                // Product Gallery Section
                Section::make('Galeri Produk')
                    ->schema([
                        Grid::make(3)->schema(function ($record) {
                            $entries = [];

                            if (is_array($record->gallery)) {
                                foreach ($record->gallery as $index => $image) {
                                    $entries[] = ImageEntry::make("gallery.{$index}")
                                        ->label("Galeri Gambar " . ($index + 1))
                                        ->disk('public')
                                        ->height(200);
                                }
                            }

                            return $entries;
                        }),
                    ])
                    ->hidden(fn ($record) => empty($record->gallery)),

                // Description Section
                Section::make('Deskripsi Produk')
                    ->schema([
                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),

                // Dimensions & Weight Section
                Section::make('Dimensi & Berat')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('weight')
                                    ->label('Berat')
                                    ->formatStateUsing(fn ($state) => "{$state} gram")
                                    ->inlineLabel(),

                                TextEntry::make('length')
                                    ->label('Panjang')
                                    ->formatStateUsing(fn ($state) => $state ? "{$state} cm" : '-')
                                    ->inlineLabel(),

                                TextEntry::make('width')
                                    ->label('Lebar')
                                    ->formatStateUsing(fn ($state) => $state ? "{$state} cm" : '-')
                                    ->inlineLabel(),

                                TextEntry::make('height')
                                    ->label('Tinggi')
                                    ->formatStateUsing(fn ($state) => $state ? "{$state} cm" : '-')
                                    ->inlineLabel(),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]),
        ]);
    }
}
