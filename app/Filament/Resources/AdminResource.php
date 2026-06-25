<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminResource\Pages;
use App\Filament\Resources\AdminResource\RelationManagers;
use App\Models\Admin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationIconColor = 'primary';  // Changed to primary for better visibility
    protected static ?string $navigationGroup = 'Manajement Admin & Pelanggan';
    protected static ?string $navigationLabel = 'Admin';
    protected static ?string $modelLabel = 'Administrator';
    protected static ?int $navigationSort = 1;

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
                    ->prefixIconColor('primary'),  // Changed to primary for consistency

                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->prefixIcon('heroicon-o-user-group')
                    ->prefixIconColor('primary'),  // Changed to primary for consistency

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255)
                    ->prefixIcon('heroicon-o-key')
                    ->prefixIconColor('primary')  // Changed to primary for consistency
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->iconColor('primary')
                    ->copyable()
                    ->copyMessage('Name copied!')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope')
                    ->iconColor('primary')  // Changed to primary for consistency
                    ->copyable()
                    ->copyMessage('Email copied!')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->color('primary')  // Changed to primary for consistency
                    ->searchable()
                    ->sortable(),

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
                    ->iconColor('gray'),  // Changed to gray for consistency
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->color('primary'),  // Changed to primary

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger'),

                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('gray'),  // Changed to gray
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
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }
}
