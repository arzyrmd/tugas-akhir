<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Cek perubahan status untuk mengisi tanggal-tanggal penting
        $oldStatus = $record->status;
        $newStatus = $data['status'];

        // Update record
        $record->update($data);

        // Jika status berubah, update tanggal yang sesuai
        if ($oldStatus !== $newStatus) {
            // Menunggu Pembayaran -> Pembayaran Berhasil
            if ($newStatus === 'PEMBAYARAN BERHASIL' && $record->payment_date === null) {
                $record->payment_date = now();
            }
            // Pembayaran Berhasil -> Dikemas
            elseif ($newStatus === 'DIKEMAS' && $record->packing_date === null) {
                $record->packing_date = now();
            }
            // Dikemas -> Dikirim
            elseif ($newStatus === 'DIKIRIM' && $record->delivery_date === null) {
                $record->delivery_date = now();
            }
            // Dikirim -> Selesai
            elseif ($newStatus === 'SELESAI' && $record->completed_date === null) {
                $record->completed_date = now();
            }

            $record->save();

            // Kirim notifikasi perubahan status
            Notification::make()
                ->title("Status pesanan berhasil diubah menjadi $newStatus")
                ->success()
                ->send();
        }

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
