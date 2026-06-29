<?php

namespace App\Filament\Resources\WholesalePaymentResource\Pages;

use App\Filament\Resources\WholesalePaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWholesalePayment extends CreateRecord
{
    protected static string $resource = WholesalePaymentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
