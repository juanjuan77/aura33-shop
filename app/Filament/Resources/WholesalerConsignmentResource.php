<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WholesalerConsignmentResource\Pages;
use App\Models\WholesaleDelivery;
use App\Models\WholesalePayment;
use App\Models\WholesaleRequest;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WholesalerConsignmentResource extends Resource
{
    protected static ?string $model           = WholesaleRequest::class;
    protected static ?string $navigationIcon  = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Consignaciones';
    protected static ?string $modelLabel      = 'Mayorista';
    protected static ?string $pluralModelLabel = 'Consignaciones';
    protected static ?int    $navigationSort  = 3;

    public static function table(Table $table): Table
    {
        return $table
            ->query(WholesaleRequest::where('status', 'approved'))
            ->columns([
                Tables\Columns\TextColumn::make('business_name')
                    ->label('Local / Mayorista')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ciudad')
                    ->sortable(),
                Tables\Columns\TextColumn::make('entregadas')
                    ->label('Entregadas')
                    ->getStateUsing(fn($record) =>
                        WholesaleDelivery::where('wholesale_request_id', $record->id)->sum('quantity')
                    )
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('vendidas')
                    ->label('Vendidas')
                    ->getStateUsing(fn($record) =>
                        WholesalePayment::where('wholesale_request_id', $record->id)->sum('quantity')
                    )
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('quedan')
                    ->label('Quedan')
                    ->getStateUsing(function ($record) {
                        $entregadas = WholesaleDelivery::where('wholesale_request_id', $record->id)->sum('quantity');
                        $vendidas   = WholesalePayment::where('wholesale_request_id', $record->id)->sum('quantity');
                        return max(0, $entregadas - $vendidas);
                    })
                    ->badge()
                    ->color('success'),
            ])
            ->actions([
                Tables\Actions\Action::make('ver')
                    ->label('Ver detalle')
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
