<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('processOrder')
                ->label('Proses Pesanan')
                ->visible(fn() => $this->record->status === 'PEMBAYARAN BERHASIL')
                ->action(function () {
                    $this->record->status = 'DIKEMAS';
                    $this->record->packing_date = now();
                    $this->record->save();

                    Notification::make()
                        ->title('Pesanan berhasil diproses')
                        ->success()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->color('success')
                ->icon('heroicon-o-check'),

            Action::make('ship')
                ->label('Kirim Pesanan')
                ->visible(fn() => $this->record->status === 'DIKEMAS')
                ->action(function () {
                    $this->record->status = 'DIKIRIM';
                    $this->record->delivery_date = now();
                    $this->record->save();

                    Notification::make()
                        ->title('Pesanan telah dikirim')
                        ->success()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->color('success')
                ->icon('heroicon-o-truck'),

            Action::make('complete')
                ->label('Selesaikan Pesanan')
                ->visible(fn() => $this->record->status === 'DIKIRIM')
                ->action(function () {
                    $this->record->status = 'SELESAI';
                    $this->record->completed_date = now();
                    $this->record->save();

                    Notification::make()
                        ->title('Pesanan telah selesai')
                        ->success()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->color('success')
                ->icon('heroicon-o-check-badge'),

            Action::make('cancel')
                ->label('Batalkan Pesanan')
                ->visible(fn() => !in_array($this->record->status, ['DIKIRIM', 'SELESAI', 'DIBATALKAN']))
                ->action(function () {
                    $this->record->status = 'DIBATALKAN';
                    $this->record->save();

                    Notification::make()
                        ->title('Pesanan telah dibatalkan')
                        ->danger()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->color('danger')
                ->icon('heroicon-o-x-mark')
                ->requiresConfirmation(),

            Actions\EditAction::make(),
        ];
    }
}
