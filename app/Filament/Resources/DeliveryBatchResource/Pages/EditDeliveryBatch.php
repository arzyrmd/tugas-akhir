<?php

namespace App\Filament\Resources\DeliveryBatchResource\Pages;

use App\Filament\Resources\DeliveryBatchResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Redirect;

class EditDeliveryBatch extends EditRecord
{
    protected static string $resource = DeliveryBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),


        ];
    }
}
