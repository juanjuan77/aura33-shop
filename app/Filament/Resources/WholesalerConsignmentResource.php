<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WholesalerConsignmentResource\Pages;
use App\Models\ConsignmentPayment;
use App\Models\WholesaleRequest;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WholesalerConsignmentResource extends Resource
{
    protected static ?string $model = WholesaleRequest::class;
    protected static ?string $navigationIcon  = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Consignaciones';
    protected static ?string $modelLabel      = 'Local';
    protected static ?string $pluralModelLabel = 'Consignaciones por local';
    protected static ?int    $navigationSort  = 5;
    protected static bool    $shouldRegisterNavigation = false;

    public static function table(Table $table): Table
    {
        return $table
            ->query(WholesaleRequest::where('status', 'approved')->withCount('consignments')->with('consignments.items', 'consignments.payments'))
            ->columns([
                Tables\Columns\TextColumn::make('business_name')
                    ->label('Local')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ciudad'),
                Tables\Columns\TextColumn::make('consignments_count')
                    ->label('Entregas')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('total_entregado')
                    ->label('Total entregado')
                    ->getStateUsing(fn($record) =>
                        '$' . number_format(
                            $record->consignments->sum(fn($c) => $c->items->sum(fn($i) => $i->quantity * $i->unit_price)),
                            0, ',', '.'
                        )
                    ),
                Tables\Columns\TextColumn::make('total_cobrado')
                    ->label('Total cobrado')
                    ->getStateUsing(fn($record) =>
                        '$' . number_format(
                            ConsignmentPayment::where('wholesale_request_id', $record->id)->sum('amount'),
                            0, ',', '.'
                        )
                    )
                    ->color('success'),
                Tables\Columns\TextColumn::make('saldo')
                    ->label('Saldo pendiente')
                    ->getStateUsing(function ($record) {
                        $entregado = $record->consignments->sum(fn($c) => $c->items->sum(fn($i) => $i->quantity * $i->unit_price));
                        $cobrado   = ConsignmentPayment::where('wholesale_request_id', $record->id)->sum('amount');
                        $saldo     = $entregado - $cobrado;
                        return $saldo > 0 ? '$' . number_format($saldo, 0, ',', '.') : '✓ Al día';
                    })
                    ->color(function ($record) {
                        $entregado = $record->consignments->sum(fn($c) => $c->items->sum(fn($i) => $i->quantity * $i->unit_price));
                        $cobrado   = ConsignmentPayment::where('wholesale_request_id', $record->id)->sum('amount');
                        return ($entregado - $cobrado) > 0 ? 'danger' : 'success';
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('ver')
                    ->label('Ver entregas')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => static::getUrl('view', ['record' => $record])),
            ])
            ->defaultSort('business_name');
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWholesalerConsignments::route('/'),
            'view'  => Pages\ViewWholesalerConsignment::route('/{record}'),
        ];
    }
}
