<?php

namespace App\Notifications;

use App\Models\CustomProductRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Filament\Notifications\Notification as FilamentNotification;

class NewCustomRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public CustomProductRequest $request)
    {
        // $this->onQueue('notifications'); // Optional, kalau kamu pakai queue khusus
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Permintaan Produk Kustom Anda Telah Diterima!')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Terima kasih! Kami telah menerima permintaan produk kustom Anda dengan judul:')
            ->line('**"' . $this->request->title . '"**')
            ->line('Tim kami akan segera meninjau permintaan Anda dan menghubungi Anda jika diperlukan.')
            ->action('Lihat Permintaan Anda', url('/custom-requests/' . $this->request->id))
            ->line('Terima kasih telah mempercayai layanan kami!');
    }

    public function toArray($notifiable): array
    {
        // Kirim juga notifikasi ke Filament Notification
        FilamentNotification::make()
            ->title('Permintaan Produk Kustom Baru')
            ->icon('heroicon-o-clipboard-document')
            ->body("Permintaan baru dari <strong>{$this->request->user->name}</strong> dengan judul: <strong>\"{$this->request->title}\"</strong>")
            ->actions([
                \Filament\Notifications\Actions\Action::make('Lihat')
                    ->label('Lihat')
                    ->url(route('filament.admin.resources.custom-product-requests.view', ['record' => $this->request->id])),
            ])
            ->persistent()
            ->info()
            ->sendToDatabase($notifiable);

        return [
            'message' => 'Permintaan produk kustom Anda dengan judul "' . $this->request->title . '" telah berhasil dikirim.',
            'title' => $this->request->title,
            'customer_name' => $this->request->user->name ?? 'Pelanggan',
            'request_id' => $this->request->id,
            'created_at' => now(),
        ];
    }
}
