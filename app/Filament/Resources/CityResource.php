<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Filament\Resources\CityResource\RelationManagers;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Wizard\Step;

class CityResource extends Resource
{
    protected static ?string $model = City::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Kota';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $pluralModelLabel = 'Kota';
    protected static ?string $modelLabel = 'Kota';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Step::make('Informasi Dasar')
                        ->description('Masukkan nama, kode, dan provinsi kota')
                        ->schema([
                            Forms\Components\Select::make('province_id')
                                ->relationship('province', 'name')
                                ->label('Provinsi')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->placeholder('Pilih provinsi...'),
                            Forms\Components\TextInput::make('name')
                                ->label('Nama Kota')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Contoh: Semarang'),
                            Forms\Components\TextInput::make('code')
                                ->label('Kode Kota')
                                ->required()
                                ->maxLength(5)
                                ->unique(ignoreRecord: true)
                                ->placeholder('Contoh: SMG01')
                                ->helperText('Kode unik untuk identifikasi kota'),
                        ])
                        ->columns(2),

                    Step::make('Informasi Tambahan')
                        ->description('Informasi tambahan dan pengaturan lainnya')
                        ->schema([
                            Forms\Components\TextInput::make('shipping_cost')
                                ->label('Biaya Pengiriman')
                                ->required()
                                ->numeric()
                                ->default(0)
                                ->suffix('IDR')
                                ->placeholder('Contoh: 15000')
                                ->helperText('Biaya dasar pengiriman ke kota ini'),
                            Forms\Components\Textarea::make('description')
                                ->label('Deskripsi')
                                ->rows(3)
                                ->placeholder('Deskripsi singkat tentang kota (opsional)')
                                ->maxLength(500),
                        ]),
                ])
                ->skippable() // ✅ Steps bisa diklik langsung
                ->persistStepInQueryString() // ✅ Step tersimpan di URL
                ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('province.name')
                    ->label('Provinsi')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kota')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode Kota')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('shipping_cost')
                    ->label('Biaya Pengiriman')
                    ->money('IDR')
                    ->sortable()
                    ->badge()
                    ->color('success'),

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
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Filter berdasarkan provinsi
                Tables\Filters\SelectFilter::make('province')
                    ->relationship('province', 'name')
                    ->preload()
                    ->label('Filter Provinsi'),

                // Filter berdasarkan nama kota
                Tables\Filters\Filter::make('name')
                    ->label('Filter Nama Kota')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Kota')
                            ->placeholder('Masukkan nama kota...')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['name'],
                            fn (Builder $query, $name): Builder => $query->where('name', 'like', '%' . $name . '%')
                        );
                    }),

                // Filter berdasarkan kode kota
                Tables\Filters\Filter::make('code')
                    ->label('Filter Kode Kota')
                    ->form([
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Kota')
                            ->placeholder('Masukkan kode kota...')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['code'],
                            fn (Builder $query, $code): Builder => $query->where('code', 'like', '%' . $code . '%')
                        );
                    }),

                // Filter berdasarkan rentang biaya pengiriman
                Tables\Filters\Filter::make('shipping_cost_range')
                    ->label('Filter Biaya Pengiriman')
                    ->form([
                        Forms\Components\TextInput::make('min_shipping_cost')
                            ->label('Biaya Minimum')
                            ->numeric()
                            ->placeholder('0'),
                        Forms\Components\TextInput::make('max_shipping_cost')
                            ->label('Biaya Maksimum')
                            ->numeric()
                            ->placeholder('100000'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_shipping_cost'],
                                fn (Builder $query, $min): Builder => $query->where('shipping_cost', '>=', $min)
                            )
                            ->when(
                                $data['max_shipping_cost'],
                                fn (Builder $query, $max): Builder => $query->where('shipping_cost', '<=', $max)
                            );
                    }),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}
