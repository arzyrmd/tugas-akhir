<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order)
    {
        // Optional: $this->onQueue('notifications');
    }

    public function via($notifiable): array
    {
        return ['database']; // hanya via database
    }

    public function toArray($notifiable): array
    {
        // Kirim ke Filament Notification
        FilamentNotification::make()
            ->title('Pesanan Baru')
            ->icon('heroicon-o-shopping-bag')
            ->body("Pesanan baru dari <strong>{$this->order->full_name}</strong> dengan kode pembayaran <strong>{$this->order->payment_code}</strong> dan total Rp" . number_format($this->order->total, 0, ',', '.'))
            ->actions([
                \Filament\Notifications\Actions\Action::make('Lihat')
                    ->label('Lihat Pesanan')
                    ->url(route('filament.admin.resources.orders.view', ['record' => $this->order->id])),
            ])
            ->persistent()
            ->danger()
            ->sendToDatabase($notifiable);

        return [
            'order_id' => $this->order->id,
            'payment_code' => $this->order->payment_code,
            'customer_name' => $this->order->full_name,
            'total' => $this->order->total,
            'status' => $this->order->status,
            'created_at' => $this->order->order_created_at,
        ];
    }
}
