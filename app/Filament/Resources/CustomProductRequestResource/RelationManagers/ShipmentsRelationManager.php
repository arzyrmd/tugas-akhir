<?php

namespace App\Filament\Resources\CustomProductRequestResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Province;
use App\Models\City;
use Filament\Tables\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShipmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'shipment';

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static bool $isScopedToParent = true;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Penerima')
                    ->schema([
                        Forms\Components\TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->required()
                            ->maxLength(20),
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Lokasi Pengiriman')
                    ->schema([
                        Forms\Components\Select::make('province_id')
                            ->label('Provinsi')
                            ->options(Province::pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('city_id', null)),
                        Forms\Components\Select::make('city_id')
                            ->label('Kota/Kabupaten')
                            ->options(function (callable $get) {
                                $provinceId = $get('province_id');
                                if (!$provinceId) {
                                    return [];
                                }
                                return City::where('province_id', $provinceId)->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('postal_code')
                            ->label('Kode Pos')
                            ->required()
                            ->maxLength(10),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Informasi Pengiriman')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        Forms\Components\TextInput::make('shipping_cost')
                            ->label('Biaya Pengiriman')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        Forms\Components\TextInput::make('total')
                            ->label('Total')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),

                        Forms\Components\TextInput::make('status')
                            ->label('Status Pengiriman')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('payment_code')
                            ->label('Kode Pembayaran')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Pengiriman')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama Penerima')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('province.name')
                    ->label('Provinsi'),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Kota'),

                Tables\Columns\TextColumn::make('shipping_cost')
                    ->label('Biaya Kirim')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status'),
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


                // Tambahkan action untuk mencetak PDF (PERBAIKAN)


            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
