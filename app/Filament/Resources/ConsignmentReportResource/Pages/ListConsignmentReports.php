<?php

namespace App\Filament\Resources\ConsignmentReportResource\Pages;

use App\Filament\Resources\ConsignmentReportResource;
use Filament\Resources\Pages\ListRecords;

class ListConsignmentReports extends ListRecords
{
    protected static string $resource = ConsignmentReportResource::class;
    protected function getHeaderActions(): array { return []; }
}
