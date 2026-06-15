<?php

namespace App\Filament\Resources\WholesaleRequestResource\Pages;

use App\Filament\Resources\WholesaleRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWholesaleRequests extends ListRecords
{
    protected static string $resource = WholesaleRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
