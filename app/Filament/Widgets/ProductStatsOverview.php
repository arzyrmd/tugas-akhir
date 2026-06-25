<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\OrderItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Resources\ProductResource;
use Illuminate\Support\Facades\DB;

class ProductStatsOverview extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $batasMinimumStok = ProductResource::$batasMinimumStok;

        // Total produk aktif dan non-aktif
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $inactiveProducts = $totalProducts - $activeProducts;

        // Produk dengan stok rendah dan habis
        $lowStockCount = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->where('stock', '<=', $batasMinimumStok)
            ->count();

        $outOfStockCount = Product::where('is_active', true)
            ->where('stock', 0)
            ->count();

        // Produk dengan stok terendah (hanya produk aktif dengan stok > 0)
        $lowestStockProduct = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('stock', 'asc')
            ->first();

        // PERBAIKAN: Produk terlaris berdasarkan penjualan aktual (OrderItem), bukan stok keluar admin
        $bestSellingData = OrderItem::select('order_items.product_id', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.is_active', true)
            ->where('orders.created_at', '>=', now()->subDays(30))
            ->groupBy('order_items.product_id')
            ->orderByDesc('total_sold')
            ->first();

        $topProduct = $bestSellingData ? Product::find($bestSellingData->product_id) : null;

        // Total nilai inventori (produk aktif)
        $totalInventoryValue = Product::where('is_active', true)
            ->selectRaw('SUM(price * stock) as total_value')
            ->first()
            ->total_value ?? 0;

        // Pergerakan stok hari ini
        $todayStockIn = StockMovement::where('type', 'in')
            ->whereDate('created_at', today())
            ->sum('quantity');

        $todayStockOut = StockMovement::where('type', 'out')
            ->whereDate('created_at', today())
            ->sum('quantity');

        return [
            Stat::make('Total Produk', number_format($totalProducts))
                ->description("{$activeProducts} aktif, {$inactiveProducts} nonaktif")
                ->descriptionIcon('heroicon-o-archive-box')
                ->chart([
                    $activeProducts > 0 ? ($activeProducts / max($totalProducts, 1)) * 100 : 0,
                    $inactiveProducts > 0 ? ($inactiveProducts / max($totalProducts, 1)) * 100 : 0
                ])
                ->color('primary'),

            Stat::make('Stok Bermasalah', number_format($lowStockCount + $outOfStockCount))
                ->description("{$lowStockCount} rendah, {$outOfStockCount} habis")
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->chart([
                    $outOfStockCount > 0 ? 100 : 0,
                    $lowStockCount > 0 ? 75 : 25,
                    50, 25
                ])
                ->color($outOfStockCount > 0 ? 'danger' : ($lowStockCount > 0 ? 'warning' : 'success')),

            Stat::make('Produk Stok Terendah', $lowestStockProduct?->name ?? 'Tidak ada')
                ->description($lowestStockProduct ?
                    "Stok: {$lowestStockProduct->stock} unit" :
                    "Semua produk stok aman"
                )
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->chart($lowestStockProduct ? [
                    $batasMinimumStok,
                    max($lowestStockProduct->stock - 2, 0),
                    max($lowestStockProduct->stock - 1, 0),
                    $lowestStockProduct->stock
                ] : [10, 8, 6, 4])
                ->color($lowestStockProduct && $lowestStockProduct->stock <= $batasMinimumStok ? 'warning' : 'success'),

            Stat::make('Produk Terlaris (30 hari)', $topProduct?->name ?? 'Belum ada penjualan')
                ->description($bestSellingData ?
                    "Terjual: " . number_format($bestSellingData->total_sold) . " item" :
                    "Periode: " . now()->subDays(30)->format('d/m') . " - " . now()->format('d/m')
                )
                ->descriptionIcon('heroicon-o-fire')
                ->chart($bestSellingData ? [
                    max($bestSellingData->total_sold - 15, 1),
                    max($bestSellingData->total_sold - 10, 2),
                    max($bestSellingData->total_sold - 5, 5),
                    $bestSellingData->total_sold
                ] : [1, 2, 3, 2])
                ->color('success'),

            Stat::make('Nilai Inventori', 'Rp ' . number_format($totalInventoryValue, 0, ',', '.'))
                ->description('Total nilai stok produk aktif')
                ->descriptionIcon('heroicon-o-banknotes')
                ->chart([
                    $totalInventoryValue > 0 ? 80 : 0,
                    $totalInventoryValue > 0 ? 85 : 0,
                    $totalInventoryValue > 0 ? 90 : 0,
                    $totalInventoryValue > 0 ? 100 : 0
                ])
                ->color('info'),

            Stat::make('Pergerakan Stok Hari Ini', number_format($todayStockIn + $todayStockOut))
                ->description("Masuk: {$todayStockIn}, Keluar: {$todayStockOut}")
                ->descriptionIcon('heroicon-o-arrows-right-left')
                ->chart([
                    $todayStockOut > 0 ? 60 : 20,
                    $todayStockIn > 0 ? 80 : 30,
                    ($todayStockIn + $todayStockOut) > 0 ? 100 : 40,
                    max($todayStockIn, $todayStockOut, 1)
                ])
                ->color(($todayStockIn + $todayStockOut) > 0 ? 'success' : 'gray'),
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return '30s'; // Refresh setiap 30 detik untuk data real-time
    }
}
