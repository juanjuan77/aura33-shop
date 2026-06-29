<?php

namespace App\Filament\Resources\WholesaleDeliveryResource\Pages;

use App\Filament\Resources\WholesaleDeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWholesaleDelivery extends EditRecord
{
    protected static string $resource = WholesaleDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
