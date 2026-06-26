<?php

namespace App\Filament\Resources\ConsignmentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';
    protected static ?string $title = 'Pagos recibidos';

    public function form(Form $form): Form
    {
        $consignment = $this->getOwnerRecord();
        $consignment->load('items');

        $itemOptions = $consignment->items->mapWithKeys(
            fn($item) => [$item->id => "{$item->product_name} (entregados: {$item->quantity})"]
        )->toArray();

        return $form->schema([
            Forms\Components\Section::make('¿Qué productos se vendieron?')
                ->schema([
                    Forms\Components\Repeater::make('items_sold')
                        ->label('')
                        ->schema([
                            Forms\Components\Select::make('consignment_item_id')
                                ->label('Producto')
                                ->options($itemOptions)
                                ->required()
                                ->reactive(),
                            Forms\Components\TextInput::make('qty_sold')
                                ->label('Cantidad vendida')
                                ->numeric()
                                ->required()
                                ->minValue(1),
                        ])
                        ->columns(2)
                        ->addActionLabel('+ Agregar producto vendido')
                        ->defaultItems(1),
                ]),

            Forms\Components\Section::make('Pago')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('amount')
                        ->label('Monto cobrado ($)')
                        ->numeric()
                        ->required()
                        ->prefix('$'),
                    Forms\Components\FileUpload::make('receipt')
                        ->label('Comprobante (foto/PDF)')
                        ->disk('public')
                        ->directory('consignment-receipts')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                        ->dehydrated(fn($state) => filled($state)),
                    Forms\Components\Textarea::make('notes')
                        ->label('Notas')
                        ->placeholder('Ej: transferencia MP 15/06')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto cobrado')
                    ->money('ARS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('items_sold')
                    ->label('Productos vendidos')
                    ->formatStateUsing(function ($state, $record) {
                        if (! $state) return '—';
                        $consignment = $record->consignment ?? $this->getOwnerRecord();
                        $consignment->load('items');
                        $itemMap = $consignment->items->keyBy('id');
                        return collect($state)->map(function ($s) use ($itemMap) {
                            $name = isset($itemMap[$s['consignment_item_id']])
                                ? $itemMap[$s['consignment_item_id']]->product_name
                                : '?';
                            return "{$name} x{$s['qty_sold']}";
                        })->join(', ');
                    }),
                Tables\Columns\IconColumn::make('receipt')
                    ->label('Comprobante')
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('heroicon-o-x-mark'),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notas')
                    ->limit(30),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('+ Registrar pago')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['wholesale_request_id'] = $this->getOwnerRecord()->wholesale_request_id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
