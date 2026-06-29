<?php

namespace App\Filament\Resources\WholesaleDeliveryResource\Pages;

use App\Filament\Resources\WholesaleDeliveryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWholesaleDelivery extends CreateRecord
{
    protected static string $resource = WholesaleDeliveryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
