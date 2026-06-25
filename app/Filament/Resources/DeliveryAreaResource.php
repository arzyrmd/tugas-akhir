<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryAreaResource\Pages;
use App\Models\DeliveryArea;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Wizard\Step;

class DeliveryAreaResource extends Resource
{
    protected static ?string $model = DeliveryArea::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'Area Pengiriman';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $pluralModelLabel = 'Area Pengiriman';
    protected static ?string $modelLabel = 'Area Pengiriman';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Step::make('Informasi Dasar')
                        ->description('Masukkan nama dan deskripsi area pengiriman')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nama Area')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Contoh: Area Jakarta Selatan'),
                            Forms\Components\Textarea::make('description')
                                ->label('Deskripsi')
                                ->rows(3)
                                ->placeholder('Deskripsi singkat tentang area pengiriman (opsional)')
                                ->maxLength(1000),
                        ])
                        ->columns(1),

                    Step::make('Pengaturan & Kota')
                        ->description('Pengaturan status dan pilih kota-kota dalam area')
                        ->schema([
                            Forms\Components\Toggle::make('is_active')
                                ->label('Status Aktif')
                                ->default(true)
                                ->helperText('Area pengiriman aktif atau tidak aktif'),
                            Forms\Components\CheckboxList::make('cities')
                                ->label('Pilih Kota-kota')
                                ->relationship('cities', 'name')
                                ->options(
                                    City::with('province')
                                        ->orderBy('name')
                                        ->get()
                                        ->mapWithKeys(function ($city) {
                                            $provinceName = $city->province->name ?? '';
                                            return [$city->id => "{$city->name} ({$provinceName})"];
                                        })
                                )
                                ->searchable()
                                ->columns(3)
                                ->helperText('Pilih kota-kota yang termasuk dalam area pengiriman ini'),
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Area')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('cities_count')
                    ->label('Jumlah Kota')
                    ->counts('cities')
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
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
                // Filter berdasarkan status
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Filter Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ]),

                // Filter berdasarkan nama area
                Tables\Filters\Filter::make('name')
                    ->label('Filter Nama Area')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Area')
                            ->placeholder('Masukkan nama area...')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['name'],
                            fn (Builder $query, $name): Builder => $query->where('name', 'like', '%' . $name . '%')
                        );
                    }),

                // Filter berdasarkan jumlah kota
                Tables\Filters\Filter::make('cities_count')
                    ->label('Filter Jumlah Kota')
                    ->form([
                        Forms\Components\TextInput::make('min_cities')
                            ->label('Minimum Kota')
                            ->numeric()
                            ->placeholder('0'),
                        Forms\Components\TextInput::make('max_cities')
                            ->label('Maksimum Kota')
                            ->numeric()
                            ->placeholder('100'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->withCount('cities')
                            ->when(
                                $data['min_cities'],
                                fn (Builder $query, $min): Builder => $query->having('cities_count', '>=', $min)
                            )
                            ->when(
                                $data['max_cities'],
                                fn (Builder $query, $max): Builder => $query->having('cities_count', '<=', $max)
                            );
                    }),

                // Filter berdasarkan kota tertentu
                Tables\Filters\SelectFilter::make('cities')
                    ->label('Filter Berdasarkan Kota')
                    ->relationship('cities', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
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
            'index' => Pages\ListDeliveryAreas::route('/'),
            'create' => Pages\CreateDeliveryArea::route('/create'),
            'edit' => Pages\EditDeliveryArea::route('/{record}/edit'),
        ];
    }
}
