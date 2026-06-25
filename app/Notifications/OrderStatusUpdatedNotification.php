<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $oldStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, string $oldStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('[' . config('app.name') . '] Status Pesanan Anda Diperbarui - ' . $this->order->payment_code)
            ->greeting('Halo ' . $this->order->full_name . '!')
            ->line('Status pesanan Anda telah diperbarui.')
            ->line('**Nomor Pesanan:** ' . $this->order->payment_code)
            ->line('**Status Sebelumnya:** ' . $this->oldStatus)
            ->line('**Status Saat Ini:** ' . $this->order->status);

        // Tambahkan pesan khusus berdasarkan status
        switch ($this->order->status) {
            case 'PEMBAYARAN BERHASIL':
                $message->line('✅ Pembayaran Anda telah kami terima dan dikonfirmasi.')
                       ->line('Pesanan Anda akan segera diproses.');
                break;

            case 'DIKEMAS':
                $message->line('📦 Pesanan Anda sedang dikemas.')
                       ->line('Tim kami sedang menyiapkan produk Anda dengan hati-hati.');
                if ($this->order->packing_date) {
                    $message->line('**Tanggal Dikemas:** ' . $this->order->packing_date->format('d/m/Y H:i'));
                }
                break;

            case 'SIAP DIKIRIM':
                $message->line('🚚 Pesanan Anda telah siap untuk dikirim.')
                       ->line('Pesanan akan segera dikirim ke alamat tujuan.');
                break;

            case 'DIKIRIM':
                $message->line('🚛 Pesanan Anda sedang dalam perjalanan!')
                       ->line('Mohon menunggu hingga pesanan sampai di alamat tujuan.');
                if ($this->order->delivery_date) {
                    $message->line('**Tanggal Pengiriman:** ' . $this->order->delivery_date->format('d/m/Y H:i'));
                }
                break;

            case 'SELESAI':
                $message->line('🎉 Pesanan Anda telah selesai!')
                       ->line('Terima kasih telah berbelanja di ' . config('app.name') . '.')
                       ->line('Kami harap Anda puas dengan produk yang diterima.');
                if ($this->order->completed_date) {
                    $message->line('**Tanggal Selesai:** ' . $this->order->completed_date->format('d/m/Y H:i'));
                }
                break;

            case 'DIBATALKAN':
                $message->line('❌ Pesanan Anda telah dibatalkan.')
                       ->line('Jika ada pertanyaan, silakan hubungi customer service kami.');
                break;
        }

        // Informasi pesanan
        $message->line('')->line('**Detail Pesanan:**')
                ->line('Total: Rp ' . number_format($this->order->total, 0, ',', '.'))
                ->line('Alamat Pengiriman: ' . $this->order->address);

        // Tambahkan catatan jika ada
        if ($this->order->notes) {
            $message->line('**Catatan:** ' . $this->order->notes);
        }

        // Button action (optional)
        if ($this->order->user_id) {
            $message->action('Lihat Pesanan', url('/orders/' . $this->order->id));
        }

        return $message->line('Terima kasih atas kepercayaan Anda!')
                      ->salutation('Salam hangat,
' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'payment_code' => $this->order->payment_code,
            'old_status' => $this->oldStatus,
            'new_status' => $this->order->status,
            'total' => $this->order->total,
            'message' => $this->getStatusMessage(),
            'created_at' => now(),
        ];
    }

    /**
     * Get status message for database notification
     */
    protected function getStatusMessage(): string
    {
        return match ($this->order->status) {
            'PEMBAYARAN BERHASIL' => 'Pembayaran pesanan Anda telah dikonfirmasi',
            'DIKEMAS' => 'Pesanan Anda sedang dikemas',
            'SIAP DIKIRIM' => 'Pesanan Anda siap untuk dikirim',
            'DIKIRIM' => 'Pesanan Anda sedang dalam perjalanan',
            'SELESAI' => 'Pesanan Anda telah selesai',
            'DIBATALKAN' => 'Pesanan Anda telah dibatalkan',
            default => 'Status pesanan Anda telah diperbarui'
        };
    }

    /**
     * Get the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'order_status_updated';
    }
}
