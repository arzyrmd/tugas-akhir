<?php

namespace App\Filament\Resources\DeliveryReportResource\Pages;

use App\Filament\Resources\DeliveryReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeliveryReports extends ListRecords
{
    protected static string $resource = DeliveryReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
