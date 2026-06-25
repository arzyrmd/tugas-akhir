<?php

namespace App\Filament\Resources\SalesReportResource\Pages;

use App\Filament\Resources\SalesReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Order;
use Carbon\Carbon;

class ListSalesReports extends ListRecords
{
    protected static string $resource = SalesReportResource::class;



    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua Transaksi')
                ->badge(Order::count()),

            'bulan_ini' => Tab::make('Bulan Ini')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereMonth('order_created_at', now()->month)
                    ->whereYear('order_created_at', now()->year))
                ->badge(Order::whereMonth('order_created_at', now()->month)
                    ->whereYear('order_created_at', now()->year)->count()),

            'minggu_ini' => Tab::make('Minggu Ini')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('order_created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek(),
                ]))
                ->badge(Order::whereBetween('order_created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek(),
                ])->count()),

            'hari_ini' => Tab::make('Hari Ini')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('order_created_at', today()))
                ->badge(Order::whereDate('order_created_at', today())->count()),

            'selesai' => Tab::make('Transaksi Selesai')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'SELESAI'))
                ->badge(Order::where('status', 'SELESAI')->count()),

            'pending' => Tab::make('Menunggu Pembayaran')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'MENUNGGU PEMBAYARAN'))
                ->badge(Order::where('status', 'MENUNGGU PEMBAYARAN')->count()),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SalesReportResource\Widgets\SalesOverviewWidget::class,
        ];
    }
}
