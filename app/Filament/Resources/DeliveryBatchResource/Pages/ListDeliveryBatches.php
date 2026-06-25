<?php

namespace App\Filament\Resources\DeliveryBatchResource\Pages;

use App\Filament\Resources\DeliveryBatchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeliveryBatches extends ListRecords
{
    protected static string $resource = DeliveryBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
