<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Pendapatan 7 Hari Terakhir';

    protected static ?int $sort = 3; // Baris kedua kanan
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
            ->sum('total');

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate / 1000),
                    'backgroundColor' => '#10B981',
                    'borderColor' => '#059669',
                    'fill' => true,
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('d/m')),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected static ?string $pollingInterval = '60s';
}
