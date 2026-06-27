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
     * Stock agrupado por producto (nombre).
     * Devuelve array: product_name => ['item_id' => int, 'stock' => int]
     */
    protected function getProductStock(): array
    {
        $consignments = $this->getConsignments();
        $itemStock = [];

        foreach ($consignments as $c) {
            foreach ($c->items as $item) {
                $itemStock[$item->id] = [
                    'name'  => $item->product?->name ?? $item->product_name,
                    'stock' => $item->quantity,
                ];
            }
        }

        // Restar vendidos en pagos anteriores
        $payments = ConsignmentPayment::where('wholesale_request_id', $this->record->id)->get();
        foreach ($payments as $pay) {
            $sold = is_string($pay->items_sold) ? json_decode($pay->items_sold, true) : $pay->items_sold;
            if (! is_array($sold)) continue;
            foreach ($sold as $s) {
                $id = (int)($s['consignment_item_id'] ?? 0);
                if (isset($itemStock[$id])) {
                    $itemStock[$id]['stock'] -= (int)($s['qty_sold'] ?? 0);
                }
            }
        }

        // Agrupar por nombre de producto, sumar stock, guardar el item_id con más stock
        $byProduct = [];
        foreach ($itemStock as $itemId => $data) {
            if ($data['stock'] <= 0) continue;
            $name = $data['name'];
            if (! isset($byProduct[$name]) || $data['stock'] > $byProduct[$name]['stock']) {
                $byProduct[$name] = ['item_id' => $itemId, 'stock' => $data['stock']];
            }
        }

        return $byProduct; // ['Botella Fluorita Verde' => ['item_id' => 3, 'stock' => 7], ...]
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('registrar_pago')
                ->label('💳 Registrar pago')
                ->color('success')
                ->form(function () {
                    $stock   = $this->getProductStock();
                    $options = collect($stock)->mapWithKeys(
                        fn($v, $name) => [$name => $name . ' (stock: ' . $v['stock'] . ' u.)']
                    )->toArray();

                    return [
                        Forms\Components\TextInput::make('amount')
                            ->label('Monto cobrado ($)')
                            ->numeric()->required()->prefix('$'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notas (opcional)')
                            ->rows(2),

                        Forms\Components\FileUpload::make('receipt')
                            ->label('Comprobante (opcional)')
                            ->acceptedFileTypes(['image/jpeg','image/png','image/webp','application/pdf'])
                            ->directory('consignment-receipts')
                            ->maxSize(5120),

                        Forms\Components\Repeater::make('items_sold')
                            ->label('Productos vendidos')
                            ->schema([
                                Forms\Components\Select::make('product_name')
                                    ->label('Producto')
                                    ->options($options)
                                    ->required()
                                    ->searchable(),
                                Forms\Components\TextInput::make('qty_sold')
                                    ->label('Unidades vendidas')
                                    ->numeric()->required()->minValue(1)->default(1),
                                Forms\Components\TextInput::make('qty_paid')
                                    ->label('Nos paga ahora')
                                    ->numeric()->required()->minValue(0)->default(1),
                            ])
                            ->columns(3)
                            ->addActionLabel('+ Agregar producto')
                            ->defaultItems(1),
                    ];
                })
                ->action(function (array $data) {
                    $stock = $this->getProductStock();

                    // Resolver consignment_item_id desde el nombre del producto
                    $itemsSold = collect($data['items_sold'] ?? [])->map(function ($row) use ($stock) {
                        $name   = $row['product_name'];
                        $itemId = $stock[$name]['item_id'] ?? null;
                        return [
                            'consignment_item_id' => $itemId,
                            'qty_sold'            => (int)$row['qty_sold'],
                            'qty_paid'            => (int)$row['qty_paid'],
                        ];
                    })->toArray();

                    ConsignmentPayment::create([
                        'wholesale_request_id' => $this->record->id,
                        'consignment_id'       => null,
                        'amount'               => $data['amount'],
                        'notes'                => $data['notes'] ?? null,
                        'receipt'              => $data['receipt'] ?? null,
                        'items_sold'           => $itemsSold,
                    ]);
                    Notification::make()->title('Pago registrado')->success()->send();
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
