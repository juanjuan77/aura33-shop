<?php

namespace App\Filament\Resources\WholesalerConsignmentResource\Pages;

use App\Filament\Resources\WholesalerConsignmentResource;
use App\Models\Consignment;
use App\Models\ConsignmentItem;
use App\Models\ConsignmentPayment;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class ViewWholesalerConsignment extends Page
{
    protected static string $resource = WholesalerConsignmentResource::class;
    protected static string $view     = 'filament.pages.wholesaler-consignment-view';

    public $record;

    public function mount(int|string $record): void
    {
        $this->record = \App\Models\WholesaleRequest::findOrFail($record);
    }

    public function getConsignments()
    {
        return Consignment::where('wholesale_request_id', $this->record->id)
            ->with(['items.product.category', 'payments'])
            ->orderByDesc('delivery_date')
            ->get();
    }

    /**
     * Calcula el stock disponible por consignment_item.
     * Devuelve array: item_id => ['label' => ..., 'stock' => int, 'unit_price' => float]
     */
    protected function getStockMap(): array
    {
        $consignments = $this->getConsignments();
        $map = [];

        foreach ($consignments as $c) {
            foreach ($c->items as $item) {
                $map[$item->id] = [
                    'label'      => ($item->product?->name ?? $item->product_name) . ' — entrega ' . ($c->delivery_date?->format('d/m/Y') ?? $c->created_at->format('d/m/Y')),
                    'stock'      => $item->quantity,
                    'unit_price' => $item->unit_price,
                ];
            }
        }

        // Restar lo ya vendido en pagos anteriores
        $payments = ConsignmentPayment::where('wholesale_request_id', $this->record->id)->get();
        foreach ($payments as $pay) {
            $sold = is_string($pay->items_sold) ? json_decode($pay->items_sold, true) : $pay->items_sold;
            if (! is_array($sold)) continue;
            foreach ($sold as $s) {
                $id = (int)($s['consignment_item_id'] ?? 0);
                if (isset($map[$id])) {
                    $map[$id]['stock'] -= (int)($s['qty_sold'] ?? 0);
                }
            }
        }

        // Solo mostrar items con stock > 0
        return array_filter($map, fn($v) => $v['stock'] > 0);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('registrar_pago')
                ->label('💳 Registrar pago')
                ->color('success')
                ->form(function () {
                    $stockMap = $this->getStockMap();
                    $options  = collect($stockMap)->mapWithKeys(
                        fn($v, $id) => [$id => $v['label'] . ' (stock: ' . $v['stock'] . ' u.)']
                    )->toArray();

                    return [
                        Forms\Components\TextInput::make('amount')
                            ->label('Monto cobrado ($)')
                            ->numeric()->required()->prefix('$'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notas (opcional)')
                            ->rows(2),

                        Forms\Components\FileUpload::make('receipt')
                            ->label('Comprobante de pago (opcional)')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg','image/png','image/webp','application/pdf'])
                            ->directory('consignment-receipts')
                            ->maxSize(5120),

                        Forms\Components\Repeater::make('items_sold')
                            ->label('Productos vendidos en este pago')
                            ->helperText('Indicá qué productos vendió el cliente y cuántos nos pagó.')
                            ->schema([
                                Forms\Components\Select::make('consignment_item_id')
                                    ->label('Producto (entrega)')
                                    ->options($options)
                                    ->required()
                                    ->searchable(),
                                Forms\Components\TextInput::make('qty_sold')
                                    ->label('Unidades vendidas')
                                    ->numeric()->required()->minValue(1)->default(1),
                                Forms\Components\TextInput::make('qty_paid')
                                    ->label('Unidades que nos paga ahora')
                                    ->numeric()->required()->minValue(0)->default(1),
                            ])
                            ->columns(3)
                            ->addActionLabel('+ Agregar producto')
                            ->defaultItems(1),
                    ];
                })
                ->action(function (array $data) {
                    ConsignmentPayment::create([
                        'wholesale_request_id' => $this->record->id,
                        'consignment_id'       => null,
                        'amount'               => $data['amount'],
                        'notes'                => $data['notes'] ?? null,
                        'receipt'              => $data['receipt'] ?? null,
                        'items_sold'           => $data['items_sold'] ?? [],
                    ]);
                    Notification::make()->title('Pago registrado correctamente')->success()->send();
                }),

            Action::make('nueva_entrega')
                ->label('+ Nueva entrega')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->form([
                    Forms\Components\DatePicker::make('delivery_date')
                        ->label('Fecha de entrega')
                        ->default(now())
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->label('Estado')
                        ->options(['active' => 'Activa', 'closed' => 'Cerrada'])
                        ->default('active')
                        ->required(),
                    Forms\Components\Textarea::make('notes')
                        ->label('Notas')
                        ->rows(2),
                    Forms\Components\Repeater::make('items')
                        ->label('Productos a entregar')
                        ->schema([
                            Forms\Components\Select::make('product_id')
                                ->label('Producto')
                                ->options(
                                    Product::with('category')
                                        ->orderBy('name')
                                        ->get()
                                        ->groupBy(fn($p) => $p->category?->name ?? 'Sin categoría')
                                        ->map(fn($g) => $g->pluck('name', 'id'))
                                        ->toArray()
                                )
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state) {
                                        $p = Product::find($state);
                                        if ($p) {
                                            $set('unit_price', $p->price_wholesale);
                                            $set('product_name', $p->name);
                                        }
                                    }
                                }),
                            Forms\Components\TextInput::make('quantity')
                                ->label('Cantidad')
                                ->numeric()->required()->default(1),
                            Forms\Components\TextInput::make('unit_price')
                                ->label('Precio unit. ($)')
                                ->numeric()->required()->prefix('$'),
                            Forms\Components\Hidden::make('product_name'),
                        ])
                        ->columns(3)
                        ->addActionLabel('+ Agregar producto')
                        ->defaultItems(1),
                ])
                ->action(function (array $data) {
                    $consignment = Consignment::create([
                        'wholesale_request_id' => $this->record->id,
                        'status'        => $data['status'],
                        'delivery_date' => $data['delivery_date'],
                        'notes'         => $data['notes'] ?? null,
                    ]);
                    foreach ($data['items'] as $item) {
                        $consignment->items()->create([
                            'product_id'   => $item['product_id'],
                            'product_name' => $item['product_name'] ?? '',
                            'quantity'     => $item['quantity'],
                            'unit_price'   => $item['unit_price'],
                        ]);
                    }
                    Notification::make()->title('Entrega registrada')->success()->send();
                }),
        ];
    }
}
