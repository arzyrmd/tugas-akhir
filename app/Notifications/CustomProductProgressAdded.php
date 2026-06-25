<?php

namespace App\Notifications;

use App\Models\CustomProductProgress;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomProductProgressAdded extends Notification implements ShouldQueue
{
    use Queueable;

    public $progress;

    public function __construct(CustomProductProgress $progress)
    {
        $this->progress = $progress;
    }

    public function via($notifiable): array
    {
        return ['mail']; // Tambah 'database' jika mau simpan notifikasi di DB
    }

    public function toMail($notifiable): MailMessage
    {
        $request = $this->progress->customProductRequest;

        return (new MailMessage)
            ->subject('Update Progres Pesanan Kustom Anda')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Ada pembaruan terbaru dari pesanan kustom Anda: ' . $request->title)
            ->line('Deskripsi Progres: ' . $this->progress->description)
            ->action('Lihat Detail', route('custom.show', ['id' => $request->id]))

            ->line('Terima kasih telah menggunakan layanan kami!');
    }

    // Tambahkan jika ingin mendukung notifikasi ke database
    // public function toDatabase($notifiable)
    // {
    //     return [
    //         'message' => 'Progres baru ditambahkan untuk pesanan Anda',
    //         'request_id' => $this->progress->custom_product_request_id,
    //     ];
    // }
}
