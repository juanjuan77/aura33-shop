<?php

namespace App\Filament\Resources\WholesalePaymentResource\Pages;

use App\Filament\Resources\WholesalePaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWholesalePayments extends ListRecords
{
    protected static string $resource = WholesalePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Registrar pago')];
    }
}
