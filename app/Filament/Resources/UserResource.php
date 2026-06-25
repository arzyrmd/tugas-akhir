<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $modelLabel = 'Pelanggan';
    protected static ?string $navigationGroup = 'Manajement Admin & Pelanggan';
    // Tambah warna untuk navigation icon
    protected static ?string $navigationIconColor = 'primary';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->prefixIcon('heroicon-o-user')
                    ->prefixIconColor('primary'),
                    Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->prefixIcon('heroicon-o-envelope')
                    ->prefixIconColor('success')
                    ->disabled(),

                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(15)
                    ->prefixIcon('heroicon-o-phone')
                    ->prefixIconColor('success')
                    ->disabled(),

                Forms\Components\TextInput::make('address')
                    ->maxLength(255)
                    ->prefixIcon('heroicon-o-home')
                    ->prefixIconColor('warning')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->icon('heroicon-o-user')
                    ->iconColor('primary'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->iconColor('success'),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->iconColor('warning'),
                    Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->iconColor('success'),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->icon('heroicon-o-home')
                    ->iconColor('warning'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-clock')
                    ->iconColor('gray'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-arrow-path')
                    ->iconColor('info'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->color('warning'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
