<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class OrderStats extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    protected static ?int $sort = 1; // Paling atas
    protected function getStats(): array
    {
        // Menghitung total pesanan hari ini
        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();

        // Menghitung pesanan yang belum diproses (status PEMBAYARAN BERHASIL)
        $pendingOrders = Order::where('status', 'PEMBAYARAN BERHASIL')->count();

        // Menghitung total pendapatan hari ini dari pesanan dengan status bukan DIBATALKAN
        $todayRevenue = Order::whereDate('payment_date', Carbon::today())
            ->whereNotIn('status', ['DIBATALKAN', 'MENUNGGU PEMBAYARAN'])
            ->sum('total');

        return [
            Stat::make('Pesanan Hari Ini', $todayOrders)
                ->description('Total pesanan yang masuk hari ini')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),

            Stat::make('Menunggu Diproses', $pendingOrders)
                ->description('Pesanan yang sudah dibayar dan siap diproses')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 0 ? 'warning' : 'success'),

            Stat::make('Pendapatan Hari Ini', 'Rp ' . number_format($todayRevenue, 0, ',', '.'))
                ->description('Total pendapatan dari pesanan hari ini')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
