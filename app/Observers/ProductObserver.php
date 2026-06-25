<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Admin;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->sendProductCreatedNotification($product);

        if ($product->stock <= 10) {
            $this->sendLowStockNotification($product);
        }
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        if ($product->isDirty('stock')) {
            $this->handleStockChange($product);
        }

        if ($product->isDirty('is_active')) {
            $this->handleStatusChange($product);
        }

        if ($product->isDirty('price')) {
            $this->handlePriceChange($product);
        }
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->sendProductDeletedNotification($product);
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        $this->sendProductRestoredNotification($product);
    }

    private function sendProductCreatedNotification(Product $product): void
    {
        $recipient = $this->getNotificationRecipient();
        if (!$recipient) return;

        Notification::make()
            ->title('Produk Baru Ditambahkan')
            ->icon('heroicon-m-sparkles')
            ->success()
            ->body("Produk '{$product->name}' telah berhasil ditambahkan ke sistem dengan harga Rp " . number_format($product->price, 0, ',', '.') . " dan stok {$product->stock} unit.")
            ->actions([
                Action::make('view')
                    ->button()
                    ->label('Lihat Detail')
                    ->url(route('filament.admin.resources.products.view', $product))
                    ->markAsRead(),
                Action::make('edit')
                    ->button()
                    ->label('Edit Produk')
                    ->url(route('filament.admin.resources.products.edit', $product))
                    ->color('warning')
                    ->markAsRead(),
            ])
            ->sendToDatabase($recipient, isEventDispatched: true);
    }

    private function handleStockChange(Product $product): void
    {
        $originalStock = $product->getOriginal('stock');
        $newStock = $product->stock;
        $difference = $newStock - $originalStock;

        if ($difference > 0) {
            $this->sendStockIncreasedNotification($product, $difference);
        } elseif ($difference < 0) {
            $this->sendStockDecreasedNotification($product, abs($difference));
        }

        if ($newStock <= 10 && $originalStock > 10 && $product->is_active) {
            $this->sendLowStockNotification($product);
        } elseif ($newStock == 0 && $originalStock > 0) {
            $this->sendOutOfStockNotification($product);
        } elseif ($newStock > 10 && $originalStock <= 10) {
            $this->sendStockSafeNotification($product);
        }
    }

    private function handleStatusChange(Product $product): void
    {
        if ($product->is_active) {
            $this->sendProductActivatedNotification($product);
        } else {
            $this->sendProductDeactivatedNotification($product);
        }
    }

    private function handlePriceChange(Product $product): void
    {
        $originalPrice = $product->getOriginal('price');
        $newPrice = $product->price;
        $difference = $newPrice - $originalPrice;
        $percentageChange = ($difference / $originalPrice) * 100;

        if (abs($percentageChange) >= 5) {
            if ($difference > 0) {
                $this->sendPriceIncreasedNotification($product, $originalPrice, $newPrice, $percentageChange);
            } else {
                $this->sendPriceDecreasedNotification($product, $originalPrice, $newPrice, abs($percentageChange));
            }
        }
    }

    private function sendLowStockNotification(Product $product): void
    {
        $cacheKey = "low_stock_notification_{$product->id}";

        if (Cache::has($cacheKey)) {
            return;
        }

        $recipient = $this->getNotificationRecipient();
        if (!$recipient) return;

        Notification::make()
            ->title('Peringatan Stok Rendah')
            ->icon('heroicon-m-exclamation-triangle')
            ->warning()
            ->body("Produk '{$product->name}' memiliki stok rendah: {$product->stock} unit. Segera lakukan restock!")
            ->actions([
                Action::make('restock')
                    ->button()
                    ->label('Restock Sekarang')
                    ->url(route('filament.admin.resources.products.edit', $product))
                    ->color('success')
                    ->markAsRead(),
                Action::make('view')
                    ->button()
                    ->label('Lihat Detail')
                    ->url(route('filament.admin.resources.products.view', $product))
                    ->markAsRead(),
            ])
            ->persistent()
            ->sendToDatabase($recipient, isEventDispatched: true);

        Cache::put($cacheKey, true, 21600);
    }

    private function sendOutOfStockNotification(Product $product): void
    {
        $recipient = $this->getNotificationRecipient();
        if (!$recipient) return;

        Notification::make()
            ->title('Stok Habis!')
            ->icon('heroicon-m-x-circle')
            ->danger()
            ->body("Produk '{$product->name}' sudah habis! Segera lakukan restock untuk menghindari kehilangan penjualan.")
            ->actions([
                Action::make('urgent_restock')
                    ->button()
                    ->label('Restock Mendesak')
                    ->url(route('filament.admin.resources.products.edit', $product))
                    ->color('danger')
                    ->markAsRead(),
            ])
            ->persistent()
            ->sendToDatabase($recipient, isEventDispatched: true);
    }

    private function sendStockIncreasedNotification(Product $product, int $increase): void
    {
        if ($increase < 10) return;

        $recipient = $this->getNotificationRecipient();
        if (!$recipient) return;

        Notification::make()
            ->title('Stok Bertambah')
            ->icon('heroicon-m-arrow-up-right')
            ->success()
            ->body("Stok produk '{$product->name}' bertambah {$increase} unit. Total stok sekarang: {$product->stock} unit.")
            ->actions([
                Action::make('view')
                    ->button()
                    ->label('Lihat Produk')
                    ->url(route('filament.admin.resources.products.view', $product))
                    ->markAsRead(),
            ])
            ->sendToDatabase($recipient, isEventDispatched: true);
    }

    private function sendStockDecreasedNotification(Product $product, int $decrease): void
    {
        if ($decrease < 10) return;

        $recipient = $this->getNotificationRecipient();
        if (!$recipient) return;

        Notification::make()
            ->title('Stok Berkurang Signifikan')
            ->icon('heroicon-m-arrow-down-left')
            ->warning()
            ->body("Stok produk '{$product->name}' berkurang {$decrease} unit. Sisa stok: {$product->stock} unit.")
            ->actions([
                Action::make('view')
                    ->button()
                    ->label('Lihat Produk')
                    ->url(route('filament.admin.resources.products.view', $product))
                    ->markAsRead(),
            ])
            ->sendToDatabase($recipient, isEventDispatched: true);
    }

    private function sendStockSafeNotification(Product $product): void
    {
        $recipient = $this->getNotificationRecipient();
        if (!$recipient) return;

        Notification::make()
            ->title('Stok Kembali Aman')
            ->icon('heroicon-m-check-circle')
            ->success()
            ->body("Stok produk '{$product->name}' kembali aman dengan {$product->stock} unit.")
            ->actions([
                Action::make('view')
                    ->button()
                    ->label('Lihat Produk')
                    ->url(route('filament.admin.resources.products.view', $product))
                    ->markAsRead(),
            ])
            ->sendToDatabase($recipient, isEventDispatched: true);
    }

    private function sendProductActivatedNotification(Product $product): void
    {
        $recipient = $this->getNotificationRecipient();
        if (!$recipient) return;

        Notification::make()
            ->title('Produk Diaktifkan')
            ->icon('heroicon-m-check-badge')
            ->success()
            ->body("Produk '{$product->name}' telah diaktifkan dan tersedia untuk dijual.")
            ->sendToDatabase($recipient, isEventDispatched: true);
    }

    private function sendProductDeactivatedNotification(Product $product): void
    {
        $recipient = $this->getNotificationRecipient();
        if (!$recipient) return;

        Notification::make()
            ->title('Produk Dinonaktifkan')
            ->icon('heroicon-m-pause-circle')
            ->warning()
            ->body("Produk '{$product->name}' telah dinonaktifkan dan tidak tersedia untuk dijual.")
            ->sendToDatabase($recipient, isEventDispatched: true);
    }

    private function sendPriceIncreasedNotification(Product $product, $oldPrice, $newPrice, $percentage): void
    {
        $recipient = $this->getNotificationRecipient();
        if (!$recipient) return;

        $oldPriceFormatted = number_format($oldPrice, 0, ',', '.');
        $newPriceFormatted = number_format($newPrice, 0, ',', '.');

        Notification::make()
            ->title('Harga Dinaikkan')
            ->icon('heroicon-m-currency-dollar')
            ->info()
            ->body("Harga produk '{$product->name}' naik " . number_format($percentage, 1) . "% dari Rp {$oldPriceFormatted} menjadi Rp {$newPriceFormatted}.")
            ->actions([
                Action::make('view')
                    ->button()
                    ->label('Lihat Detail')
                    ->url(route('filament.admin.resources.products.view', $product))
                    ->markAsRead(),
            ])
            ->sendToDatabase($recipient, isEventDispatched: true);
    }

    private function sendPriceDecreasedNotification(Product $product, $oldPrice, $newPrice, $percentage): void
    {
        $recipient = $this->getNotificationRecipient();
        if (!$recipient) return;

        $oldPriceFormatted = number_format($oldPrice, 0, ',', '.');
        $newPriceFormatted = number_format($newPrice, 0, ',', '.');

        Notification::make()
            ->title('Harga Diturunkan')
            ->icon('heroicon-m-tag')
            ->success()
            ->body("Harga produk '{$product->name}' turun " . number_format($percentage, 1) . "% dari Rp {$oldPriceFormatted} menjadi Rp {$newPriceFormatted}.")
            ->actions([
                Action::make('view')
                    ->button()
                    ->label('Lihat Detail')
                    ->url(route('filament.admin.resources.products.view', $product))
                    ->markAsRead(),
            ])
            ->sendToDatabase($recipient, isEventDispatched: true);
    }

    private function sendProductDeletedNotification(Product $product): void
    {
        $recipient = $this->getNotificationRecipient();
        if (!$recipient) return;

       Notification::make()
    ->title('Produk Dihapus')
    ->icon('heroicon-m-trash')
    ->danger()
    ->body("Produk '{$product->name}' telah dihapus dari sistem.")
    ->actions([
        Action::make('back_to_list')
            ->button()
            ->label('Kembali ke Daftar')
            ->url(route('filament.admin.resources.products.index'))
            ->markAsRead(),
        Action::make('create_new')
            ->button()
            ->label('Tambah Produk Baru')
            ->url(route('filament.admin.resources.products.create'))
            ->color('primary')
            ->markAsRead(),
    ])
    ->sendToDatabase($recipient, isEventDispatched: true);

    }

    private function sendProductRestoredNotification(Product $product): void
    {
        $recipient = $this->getNotificationRecipient();
        if (!$recipient) return;

        Notification::make()
            ->title('Produk Dipulihkan')
            ->icon('heroicon-m-arrow-uturn-left')
            ->success()
            ->body("Produk '{$product->name}' telah berhasil dipulihkan.")
            ->actions([
                Action::make('view')
                    ->button()
                    ->label('Lihat Produk')
                    ->url(route('filament.admin.resources.products.view', $product))
                    ->markAsRead(),
            ])
            ->sendToDatabase($recipient, isEventDispatched: true);
    }

    private function getNotificationRecipient()
    {
        if (Auth::guard('admin')->check()) {
            return Auth::guard('admin')->user();
        }

        return Admin::first();
    }
}
