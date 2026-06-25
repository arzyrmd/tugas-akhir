<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestOrders extends BaseWidget
{
    protected static ?string $heading = 'Pesanan Terbaru';

    protected static ?int $sort = 4; // Paling bawah
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->latest('created_at')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('payment_code')
                    ->label('No. Pesanan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Pelanggan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'MENUNGGU PEMBAYARAN' => 'warning',
                        'PEMBAYARAN BERHASIL' => 'success',
                        'SIAP DIKIRIM' => 'info',
                        'DIKEMAS' => 'info',
                        'DIKIRIM' => 'primary',
                        'SELESAI' => 'success',
                        'DIBATALKAN' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Order $record): string => route('filament.admin.resources.orders.view', $record))
                    ->icon('heroicon-m-eye'),
            ])
            ->paginated(false);
    }
}
