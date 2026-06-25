<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProvinceResource\Pages;
use App\Filament\Resources\ProvinceResource\RelationManagers;
use App\Models\Province;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Wizard\Step;

class ProvinceResource extends Resource
{
    protected static ?string $model = Province::class;
    protected static ?string $navigationIcon = 'heroicon-o-globe-europe-africa';
    protected static ?string $navigationLabel = 'Provinsi';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $pluralModelLabel = 'Provinsi';
    protected static ?string $modelLabel = 'Provinsi';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Step::make('Informasi Dasar')
                        ->description('Masukkan nama dan kode provinsi')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nama Provinsi')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Contoh: Jawa Tengah'),
                            Forms\Components\TextInput::make('code')
                                ->label('Kode Provinsi')
                                ->required()
                                ->maxLength(5)
                                ->unique(ignoreRecord: true)
                                ->placeholder('Contoh: JT001')
                                ->helperText('Kode unik untuk identifikasi provinsi'),
                        ])
                        ->columns(2),

                    Step::make('Informasi Tambahan')
                        ->description('Informasi tambahan dan pengaturan lainnya')
                        ->schema([
                            Forms\Components\Textarea::make('description')
                                ->label('Deskripsi')
                                ->rows(3)
                                ->placeholder('Deskripsi singkat tentang provinsi (opsional)')
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Provinsi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode Provinsi')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('cities_count')
                    ->label('Jumlah Kota')
                    ->counts('cities')
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
            ->defaultSort('name')
            ->filters([
                // Filter berdasarkan nama provinsi
                Tables\Filters\Filter::make('name')
                    ->label('Filter Nama Provinsi')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Provinsi')
                            ->placeholder('Masukkan nama provinsi...')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['name'],
                            fn (Builder $query, $name): Builder => $query->where('name', 'like', '%' . $name . '%')
                        );
                    }),

                // Filter berdasarkan kode provinsi
                Tables\Filters\Filter::make('code')
                    ->label('Filter Kode Provinsi')
                    ->form([
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Provinsi')
                            ->placeholder('Masukkan kode provinsi...')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['code'],
                            fn (Builder $query, $code): Builder => $query->where('code', 'like', '%' . $code . '%')
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
            RelationManagers\CitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProvinces::route('/'),
            'create' => Pages\CreateProvince::route('/create'),
            'edit' => Pages\EditProvince::route('/{record}/edit'),

        ];
    }
}
