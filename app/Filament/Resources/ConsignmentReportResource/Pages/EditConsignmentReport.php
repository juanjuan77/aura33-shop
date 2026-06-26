<?php

namespace App\Filament\Resources\ConsignmentReportResource\Pages;

use App\Filament\Resources\ConsignmentReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConsignmentReport extends EditRecord
{
    protected static string $resource = ConsignmentReportResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
