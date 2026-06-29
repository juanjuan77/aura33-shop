<?php

namespace App\Filament\Resources\WholesalePaymentResource\Pages;

use App\Filament\Resources\WholesalePaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWholesalePayment extends EditRecord
{
    protected static string $resource = WholesalePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
