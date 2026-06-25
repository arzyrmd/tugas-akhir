<?php

namespace App\Notifications;

use App\Models\CustomProductRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomRequestStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public CustomProductRequest $request,
        public string $oldStatus
    ) {
        // Optional: set queue
        // $this->onQueue('notifications');
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

            public function toMail($notifiable): MailMessage
        {
            $mailMessage = (new MailMessage)
                ->subject('Pembaruan Status Permintaan Produk Kustom')
                ->greeting("Halo {$notifiable->name},")
                ->line("Status permintaan Anda dengan judul \"{$this->request->title}\" telah diperbarui.")
                ->line("Dari: **{$this->translateStatus($this->oldStatus)}**")
                ->line("Menjadi: **{$this->translateStatus($this->request->status)}**");

           switch ($this->request->status) {
            case 'MENUNGGU_DP':
                $mailMessage->line("Silakan melakukan pembayaran *uang muka (DP)* agar permintaan Anda dapat segera kami proses.");
                break;
            case 'MENUNGGU_PELUNASAN':
                $mailMessage->line("Produk Anda telah selesai dikerjakan. Harap melakukan *pelunasan pembayaran* agar dapat kami lanjutkan ke proses pengiriman.");
                break;
            case 'PENAWARAN_DIBERIKAN':
                $mailMessage->line("Silakan tinjau penawaran yang telah kami ajukan dan lakukan konfirmasi.");
                break;
            case 'DIKIRIM':
                $mailMessage->line("Pesanan Anda sedang dalam proses pengiriman. Silakan cek detail untuk pelacakan.");
                break;
            case 'SELESAI':
                $mailMessage->line("Terima kasih! Permintaan produk kustom Anda telah berhasil diselesaikan.");
                break;
            case 'DIBATALKAN':
                $mailMessage->line("Permintaan ini telah dibatalkan. Hubungi kami jika ada hal yang ingin Anda diskusikan lebih lanjut.");
                break;
        }

            $mailMessage
                ->action('Lihat Detail Permintaan', route('custom.show', ['id' => $this->request->id]))
                ->line('Terima kasih telah menggunakan layanan produk kustom kami.');

            return $mailMessage;
        }

    protected function translateStatus(string $status): string
    {
        return match ($status) {
            'MENUNGGU_REVIEW'     => 'Menunggu Tinjauan',
            'PENAWARAN_DIBERIKAN' => 'Penawaran Telah Diberikan',
            'PENAWARAN_DITOLAK'   => 'Penawaran Ditolak',
            'MENUNGGU_DP'         => 'Menunggu Pembayaran DP',
            'DALAM_PENGERJAAN'    => 'Sedang Dikerjakan',
            'MENUNGGU_PELUNASAN'  => 'Menunggu Pelunasan',
            'SIAP_DIKIRIM'        => 'Siap Dikirim',
            'DIKIRIM'             => 'Sedang Dikirim',
            'SELESAI'             => 'Selesai',
            'DIBATALKAN'          => 'Dibatalkan',
            default               => ucfirst(strtolower(str_replace('_', ' ', $status))),
        };
    }


    public function toArray($notifiable): array
    {
        return [
            'request_id' => $this->request->id,
            'title' => $this->request->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->request->status,
            'created_at' => now(),
        ];
    }
}
