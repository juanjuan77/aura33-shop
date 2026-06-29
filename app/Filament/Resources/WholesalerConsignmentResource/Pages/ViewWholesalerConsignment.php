<?php

namespace App\Filament\Resources\WholesalerConsignmentResource\Pages;

use App\Filament\Resources\WholesalerConsignmentResource;
use App\Models\WholesaleDelivery;
use App\Models\WholesalePayment;
use App\Models\WholesaleRequest;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class ViewWholesalerConsignment extends Page
{
    protected static string $resource = WholesalerConsignmentResource::class;
    protected static string $view     = 'filament.pages.wholesaler-consignment-view';

    public WholesaleRequest $record;

    public function mount(int|string $record): void
    {
        $this->record = WholesaleRequest::findOrFail($record);
    }

    public function getTitle(): string
    {
        return $this->record->business_name;
    }

    public function getDeliveries()
    {
        return WholesaleDelivery::where('wholesale_request_id', $this->record->id)
            ->orderByDesc('date')->orderByDesc('created_at')->get();
    }

    public function getPayments()
    {
        return WholesalePayment::where('wholesale_request_id', $this->record->id)
            ->orderByDesc('date')->orderByDesc('created_at')->get();
    }

    public function getTotals(): array
    {
        $entregadas  = WholesaleDelivery::where('wholesale_request_id', $this->record->id)->sum('quantity');
        $vendidas    = WholesalePayment::where('wholesale_request_id', $this->record->id)->sum('quantity');
        $totalPagado = WholesalePayment::where('wholesale_request_id', $this->record->id)->sum('amount');
        return [
            'entregadas'   => $entregadas,
            'vendidas'     => $vendidas,
            'quedan'       => max(0, $entregadas - $vendidas),
            'total_pagado' => $totalPagado,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('nueva_entrega')
                ->label('+ Nueva entrega')
                ->color('primary')
                ->icon('heroicon-o-truck')
                ->form([
                    Forms\Components\DatePicker::make('date')
                        ->label('Fecha de entrega')
                        ->default(now())
                        ->required()
                        ->displayFormat('d/m/Y'),
                    Forms\Components\TextInput::make('quantity')
                        ->label('Cantidad de botellas')
                        ->numeric()
                        ->minValue(1)
                        ->required(),
                    Forms\Components\Textarea::make('notes')
                        ->label('Detalle (opcional)')
                        ->placeholder('Qué botellas, colores, modelos...')
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    WholesaleDelivery::create([
                        'wholesale_request_id' => $this->record->id,
                        'date'                 => $data['date'],
                        'quantity'             => $data['quantity'],
                        'notes'                => $data['notes'] ?? null,
                    ]);
                    Notification::make()->title('Entrega registrada')->success()->send();
                }),

            Action::make('registrar_pago')
                ->label('💳 Registrar pago')
                ->color('success')
                ->form([
                    Forms\Components\DatePicker::make('date')
                        ->label('Fecha del pago')
                        ->default(now())
                        ->required()
                        ->displayFormat('d/m/Y'),
                    Forms\Components\TextInput::make('product_name')
                        ->label('Producto')
                        ->placeholder('Ej: Botella violeta 500ml')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('quantity')
                        ->label('Cantidad vendida')
                        ->numeric()
                        ->minValue(1)
                        ->required(),
                    Forms\Components\TextInput::make('amount')
                        ->label('Importe total ($)')
                        ->numeric()
                        ->prefix('$')
                        ->required(),
                    Forms\Components\FileUpload::make('receipt')
                        ->label('Comprobante')
                        ->disk('public')
                        ->directory('wholesale-payment-receipts')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf']),
                ])
                ->action(function (array $data) {
                    WholesalePayment::create([
                        'wholesale_request_id' => $this->record->id,
                        'date'                 => $data['date'],
                        'product_name'         => $data['product_name'],
                        'quantity'             => $data['quantity'],
                        'amount'               => $data['amount'],
                        'receipt'              => $data['receipt'] ?? null,
                    ]);
                    Notification::make()->title('Pago registrado')->success()->send();
                }),

            Action::make('volver')
                ->label('← Todos los mayoristas')
                ->color('gray')
                ->url(WholesalerConsignmentResource::getUrl('index')),
        ];
    }
}
