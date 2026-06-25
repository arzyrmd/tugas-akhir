<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class OrdersChart extends ChartWidget
{
    protected static ?string $heading = 'Pesanan 7 Hari Terakhir';

    protected static ?int $sort = 2; // Baris kedua kiri
    protected int | string | array $columnSpan = [
        'sm' => 'full',
        'md' => 1,
        'lg' => 1,
    ];

    protected function getData(): array
    {
        $data = Trend::model(Order::class)
            ->between(
                start: now()->subDays(6)->startOfDay(),
                end: now()->endOfDay(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pesanan',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#F59E0B',
                    'borderColor' => '#D97706',
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('d/m')),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected static ?string $pollingInterval = '60s';
}
