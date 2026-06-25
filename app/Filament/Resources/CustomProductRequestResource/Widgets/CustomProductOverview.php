<?php

namespace App\Filament\Resources\CustomProductRequestResource\Widgets;

use App\Models\CustomProductRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class CustomProductOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Hitung statistik
        $totalRequests = CustomProductRequest::count();
        $pendingReview = CustomProductRequest::where('status', 'MENUNGGU_REVIEW')->count();
        $inProgress = CustomProductRequest::where('status', 'DALAM_PENGERJAAN')->count();
        $readyToShip = CustomProductRequest::where('status', 'SIAP_DIKIRIM')->count();
        $shipped = CustomProductRequest::where('status', 'DIKIRIM')->count();
        $completed = CustomProductRequest::where('status', 'SELESAI')->count();

        // Hitung permintaan bulan ini
        $thisMonth = CustomProductRequest::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Hitung total nilai permintaan yang disetujui
        $totalValue = CustomProductRequest::whereNotNull('quoted_price')
            ->whereIn('status', ['MENUNGGU_DP', 'DALAM_PENGERJAAN', 'MENUNGGU_PELUNASAN', 'SIAP_DIKIRIM', 'DIKIRIM', 'SELESAI'])
            ->sum('quoted_price');

        return [
            Stat::make('Total Permintaan', $totalRequests)
                ->description('Jumlah permintaan produk kustom')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Menunggu Review', $pendingReview)
                ->description('Perlu ditinjau')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Dalam Pengerjaan', $inProgress)
                ->description('Sedang diproses')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('info'),

            Stat::make('Siap/Sedang Dikirim', $readyToShip + $shipped)
                ->description('Menunggu konfirmasi penerimaan')
                ->descriptionIcon('heroicon-m-truck')
                ->color('purple'),

            Stat::make('Permintaan Bulan Ini', $thisMonth)
                ->description('Dibuat bulan ' . Carbon::now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),

            Stat::make('Total Nilai Disetujui', 'Rp ' . number_format($totalValue, 0, ',', '.'))
                ->description('Nilai permintaan yang disetujui')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }
}
