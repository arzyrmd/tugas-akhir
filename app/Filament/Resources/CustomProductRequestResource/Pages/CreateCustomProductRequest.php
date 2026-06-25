<?php

namespace App\Filament\Resources\CustomProductRequestResource\Pages;

use App\Filament\Resources\CustomProductRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class CreateCustomProductRequest extends CreateRecord
{
    protected static string $resource = CustomProductRequestResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['status'] = 'MENUNGGU_REVIEW';

        return static::getModel()::create($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        // Tampilkan notifikasi setelah berhasil membuat record
        Notification::make()
            ->title('Permintaan produk kustom berhasil dibuat')
            ->success()
            ->send();
    }
}
