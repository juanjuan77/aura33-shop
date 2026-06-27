<?php

namespace App\Filament\Resources\WholesalerConsignmentResource\Pages;

use App\Filament\Resources\WholesalerConsignmentResource;
use Filament\Resources\Pages\ListRecords;

class ListWholesalerConsignments extends ListRecords
{
    protected static string $resource = WholesalerConsignmentResource::class;

    protected function getHeaderActions(): array { return []; }
}
