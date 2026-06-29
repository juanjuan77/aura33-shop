<?php

namespace App\Filament\Resources\WholesaleDeliveryResource\Pages;

use App\Filament\Resources\WholesaleDeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWholesaleDeliveries extends ListRecords
{
    protected static string $resource = WholesaleDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Nueva entrega')];
    }
}
