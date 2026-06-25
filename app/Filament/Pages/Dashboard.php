<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\LatestOrders;
use App\Filament\Widgets\OrdersChart;
use App\Filament\Widgets\OrderStats;
use App\Filament\Widgets\RevenueChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getWidgets(): array
    {
        return [
            OrderStats::class,
            OrdersChart::class,
            RevenueChart::class,
            LatestOrders::class,
        ];
    }
}
