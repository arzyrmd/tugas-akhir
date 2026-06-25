<?php

namespace App\Filament\Resources\SalesReportResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\SalesReportView;
use App\Models\CustomProductRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Data bulan ini dan perbandingan - gunakan SalesReportView
        $thisMonth = now();
        $lastMonth = now()->subMonth();

        // Revenue data untuk 12 bulan terakhir
        $revenueData = $this->getMonthlyRevenueData();
        $orderCountData = $this->getMonthlyOrderCountData();
        $weeklyData = $this->getWeeklyData();
        $dailyDataThisMonth = $this->getDailyDataThisMonth();

        // Current month stats - gunakan SalesReportView dengan total_amount
        $thisMonthRevenue = \App\Models\SalesReportView::whereMonth('order_created_at', $thisMonth->month)
            ->whereYear('order_created_at', $thisMonth->year)
            ->sum('total_amount');

        $thisMonthOrders = \App\Models\SalesReportView::whereMonth('order_created_at', $thisMonth->month)
            ->whereYear('order_created_at', $thisMonth->year)
            ->count();

        // Last month stats for comparison
        $lastMonthRevenue = \App\Models\SalesReportView::whereMonth('order_created_at', $lastMonth->month)
            ->whereYear('order_created_at', $lastMonth->year)
            ->sum('total_amount');

        $lastMonthOrders = \App\Models\SalesReportView::whereMonth('order_created_at', $lastMonth->month)
            ->whereYear('order_created_at', $lastMonth->year)
            ->count();

        // Calculate percentage changes
        $revenueChange = $lastMonthRevenue > 0
            ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : ($thisMonthRevenue > 0 ? 100 : 0);

        $ordersChange = $lastMonthOrders > 0
            ? (($thisMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100
            : ($thisMonthOrders > 0 ? 100 : 0);

        // Today's data - gunakan SalesReportView
        $todayRevenue = \App\Models\SalesReportView::whereDate('order_created_at', today())->sum('total_amount');
        $todayOrders = \App\Models\SalesReportView::whereDate('order_created_at', today())->count();

        // Order status distribution
        $orderStatusStats = $this->getOrderStatusStats();
        $customProductStats = $this->getCustomProductStats();

        // Average order value trend
        $avgOrderValueData = $this->getAverageOrderValueData();

        return [
            // Revenue trend dengan chart 12 bulan
            Stat::make('Total Revenue Bulan Ini', 'Rp ' . number_format($thisMonthRevenue, 0, ',', '.'))
                ->description($revenueChange >= 0 ? '+' . number_format($revenueChange, 1) . '% dari bulan lalu' : number_format($revenueChange, 1) . '% dari bulan lalu')
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger')
                ->chart($revenueData['values']),

            // Order count dengan chart 12 bulan
            Stat::make('Jumlah Pesanan Bulan Ini', number_format($thisMonthOrders))
                ->description($ordersChange >= 0 ? '+' . number_format($ordersChange, 1) . '% dari bulan lalu' : number_format($ordersChange, 1) . '% dari bulan lalu')
                ->descriptionIcon($ordersChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($ordersChange >= 0 ? 'success' : 'danger')
                ->chart($orderCountData),

            // Daily sales trend bulan ini
            Stat::make('Penjualan Hari Ini', 'Rp ' . number_format($todayRevenue, 0, ',', '.'))
                ->description($todayOrders . ' pesanan hari ini')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info')
                ->chart($dailyDataThisMonth),

            // Average order value dengan trend
            Stat::make('Rata-rata per Transaksi', $thisMonthOrders > 0 ? 'Rp ' . number_format($thisMonthRevenue / $thisMonthOrders, 0, ',', '.') : 'Rp 0')
                ->description('Trend 6 bulan terakhir')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('primary')
                ->chart($avgOrderValueData),

            // Weekly performance
            Stat::make('Performa Mingguan', 'Rp ' . number_format($weeklyData['current_week'], 0, ',', '.'))
                ->description('Minggu ini vs minggu lalu')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($weeklyData['change'] >= 0 ? 'success' : 'danger')
                ->chart($weeklyData['chart_data']),

            // Custom product requests dengan status breakdown
            Stat::make('Produk Kustom', $customProductStats['total'] . ' Total')
                ->description($customProductStats['active'] . ' aktif, ' . $customProductStats['completed'] . ' selesai')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('warning')
                ->chart($customProductStats['monthly_data']),

            // Order completion rate
            Stat::make('Tingkat Penyelesaian', number_format($orderStatusStats['completion_rate'], 1) . '%')
                ->description($orderStatusStats['completed'] . ' dari ' . $orderStatusStats['total'] . ' pesanan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart($orderStatusStats['monthly_completion']),

            // Payment status distribution
            Stat::make('Status Pembayaran', $orderStatusStats['paid'] . ' Lunas')
                ->description($orderStatusStats['pending_payment'] . ' menunggu, ' . $orderStatusStats['partial'] . ' DP')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info')
                ->chart($orderStatusStats['payment_trend']),
        ];
    }

    private function getMonthlyRevenueData(): array
    {
        // Gunakan SalesReportView yang memiliki kolom total_amount
        $data = \App\Models\SalesReportView::select(
                DB::raw('YEAR(order_created_at) as year'),
                DB::raw('MONTH(order_created_at) as month'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('order_created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $values = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $found = $data->where('year', $date->year)->where('month', $date->month)->first();
            $values[] = $found ? $found->revenue / 1000000 : 0; // Convert to millions
        }

        return ['values' => $values];
    }

    private function getMonthlyOrderCountData(): array
    {
        // Gunakan SalesReportView
        $data = \App\Models\SalesReportView::select(
                DB::raw('YEAR(order_created_at) as year'),
                DB::raw('MONTH(order_created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('order_created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $values = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $found = $data->where('year', $date->year)->where('month', $date->month)->first();
            $values[] = $found ? $found->count : 0;
        }

        return $values;
    }

    private function getDailyDataThisMonth(): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        // Gunakan SalesReportView
        $data = \App\Models\SalesReportView::select(
                DB::raw('DATE(order_created_at) as date'),
                DB::raw('SUM(total_amount) as daily_revenue')
            )
            ->whereBetween('order_created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $values = [];
        $currentDate = $startOfMonth->copy();

        while ($currentDate <= now()) {
            $dateStr = $currentDate->format('Y-m-d');
            $values[] = isset($data[$dateStr]) ? $data[$dateStr]->daily_revenue / 1000000 : 0;
            $currentDate->addDay();
        }

        return $values;
    }

    private function getWeeklyData(): array
    {
        $thisWeekStart = now()->startOfWeek();
        $lastWeekStart = now()->subWeek()->startOfWeek();

        // Gunakan SalesReportView
        $thisWeek = \App\Models\SalesReportView::whereBetween('order_created_at', [
            $thisWeekStart,
            $thisWeekStart->copy()->endOfWeek()
        ])->sum('total_amount');

        $lastWeek = \App\Models\SalesReportView::whereBetween('order_created_at', [
            $lastWeekStart,
            $lastWeekStart->copy()->endOfWeek()
        ])->sum('total_amount');

        // Get last 8 weeks data for chart
        $weeklyData = [];
        for ($i = 7; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();

            $weekRevenue = \App\Models\SalesReportView::whereBetween('order_created_at', [$weekStart, $weekEnd])
                ->sum('total_amount');

            $weeklyData[] = $weekRevenue / 1000000;
        }

        $change = $lastWeek > 0 ? (($thisWeek - $lastWeek) / $lastWeek) * 100 : 0;

        return [
            'current_week' => $thisWeek,
            'last_week' => $lastWeek,
            'change' => $change,
            'chart_data' => $weeklyData
        ];
    }

    private function getAverageOrderValueData(): array
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            // Gunakan SalesReportView
            $monthlyStats = \App\Models\SalesReportView::whereMonth('order_created_at', $date->month)
                ->whereYear('order_created_at', $date->year)
                ->selectRaw('AVG(total_amount) as avg_value')
                ->first();

            $data[] = $monthlyStats->avg_value ? $monthlyStats->avg_value / 1000000 : 0;
        }

        return $data;
    }

    private function getOrderStatusStats(): array
    {
        // Gunakan SalesReportView yang memiliki data lengkap dan kolom yang benar
        $total = \App\Models\SalesReportView::count();
        $completed = \App\Models\SalesReportView::where('status', 'SELESAI')->count();

        // Gunakan payment_status dari SalesReportView
        $paid = \App\Models\SalesReportView::where('payment_status', 'LUNAS')->count();
        $pendingPayment = \App\Models\SalesReportView::where('payment_status', 'BELUM DIBAYAR')->count();
        $partial = \App\Models\SalesReportView::where('payment_status', 'DP DIBAYAR')->count();

        // Monthly completion rates for chart
        $monthlyCompletion = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthTotal = \App\Models\SalesReportView::whereMonth('order_created_at', $date->month)
                ->whereYear('order_created_at', $date->year)
                ->count();

            $monthCompleted = \App\Models\SalesReportView::whereMonth('order_created_at', $date->month)
                ->whereYear('order_created_at', $date->year)
                ->where('status', 'SELESAI')
                ->count();

            $monthlyCompletion[] = $monthTotal > 0 ? ($monthCompleted / $monthTotal) * 100 : 0;
        }

        // Payment trend - gunakan SalesReportView
        $paymentTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthPaid = \App\Models\SalesReportView::whereMonth('order_created_at', $date->month)
                ->whereYear('order_created_at', $date->year)
                ->where('payment_status', 'LUNAS')
                ->count();

            $paymentTrend[] = $monthPaid;
        }

        return [
            'total' => $total,
            'completed' => $completed,
            'completion_rate' => $total > 0 ? ($completed / $total) * 100 : 0,
            'paid' => $paid,
            'pending_payment' => $pendingPayment,
            'partial' => $partial,
            'monthly_completion' => $monthlyCompletion,
            'payment_trend' => $paymentTrend
        ];
    }

    private function getCustomProductStats(): array
    {
        $total = CustomProductRequest::count();
        $active = CustomProductRequest::whereIn('status', [
            'MENUNGGU_REVIEW',
            'PENAWARAN_DIBERIKAN',
            'DALAM_PENGERJAAN'
        ])->count();

        $completed = CustomProductRequest::where('status', 'SELESAI')->count();

        // Monthly custom product requests
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyCount = CustomProductRequest::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $monthlyData[] = $monthlyCount;
        }

        return [
            'total' => $total,
            'active' => $active,
            'completed' => $completed,
            'monthly_data' => $monthlyData
        ];
    }

    protected function getColumns(): int
    {
        return 4; // Changed from 3 to 4 for better layout with 8 stats
    }
}
