<?php

namespace App\Filament\Resources\WholesalerConsignmentResource\Pages;

use App\Filament\Resources\WholesalerConsignmentResource;
use App\Models\Consignment;
use App\Models\ConsignmentItem;
use App\Models\ConsignmentPayment;
use App\Models\Product;
use App\Notifications\NewConsignmentDeliveryNotification;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Notification as LaravelNotification;

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

    /** Stock disponible por producto (agrupado, sin mencionar entregas) */
    protected function getProductStock(int $excludePaymentId = null): array
    {
        $consignments = $this->getConsignments();
        $itemStock    = [];

        foreach ($consignments as $c) {
            foreach ($c->items as $item) {
                $itemStock[$item->id] = [
                    'name'  => $item->product?->name ?? $item->product_name,
                    'stock' => $item->quantity,
                ];
            }
        }

        $payments = ConsignmentPayment::where('wholesale_request_id', $this->record->id)->get();
        foreach ($payments as $pay) {
            if ($excludePaymentId && $pay->id === $excludePaymentId) continue;
            $sold = is_string($pay->items_sold) ? json_decode($pay->items_sold, true) : $pay->items_sold;
            if (! is_array($sold)) continue;
            foreach ($sold as $s) {
                $id = (int)($s['consignment_item_id'] ?? 0);
                if (isset($itemStock[$id])) {
                    $itemStock[$id]['stock'] -= (int)($s['qty_sold'] ?? 0);
                }
            }
        }

        $byProduct = [];
        foreach ($itemStock as $itemId => $data) {
            $name = $data['name'];
            if (! isset($byProduct[$name])) {
                $byProduct[$name] = ['item_id' => $itemId, 'stock' => 0];
            }
            $byProduct[$name]['stock'] += max(0, $data['stock']);
            // Preferir el item_id con más stock
            if ($data['stock'] > ($byProduct[$name]['stock_raw'] ?? 0)) {
                $byProduct[$name]['item_id']    = $itemId;
                $byProduct[$name]['stock_raw']  = $data['stock'];
            }
        }

        return array_filter($byProduct, fn($v) => $v['stock'] > 0);
    }

    /** Formulario para registrar pago */
    protected function paymentForm(array $stockOptions): array
    {
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
                ->helperText('Si cobra una deuda anterior sin vender nada nuevo, dejá vacío.')
                ->schema([
                    Forms\Components\Select::make('product_name')
                        ->label('Producto')
                        ->options($stockOptions)
                        ->required()
                        ->searchable(),
                    Forms\Components\TextInput::make('qty_sold')
                        ->label('Vendidas')
                        ->numeric()->required()->minValue(1)->default(1),
                    Forms\Components\TextInput::make('qty_paid')
                        ->label('Paga ahora')
                        ->numeric()->required()->minValue(0)->default(1),
                ])
                ->columns(3)
                ->addActionLabel('+ Agregar producto')
                ->defaultItems(0),
        ];
    }

    /** Convierte items_sold del form a array para guardar */
    protected function resolveItemsSold(array $formItems, array $stock): array
    {
        return collect($formItems)->map(function ($row) use ($stock) {
            $name   = $row['product_name'];
            $itemId = $stock[$name]['item_id'] ?? null;
            return [
                'consignment_item_id' => $itemId,
                'qty_sold'            => (int)($row['qty_sold'] ?? 0),
                'qty_paid'            => (int)($row['qty_paid'] ?? 0),
            ];
        })->filter(fn($r) => $r['consignment_item_id'])->values()->toArray();
    }

    /** Calcula unidades pendientes de cobro por producto */
    public function getPendingUnits(): array
    {
        $consignments = $this->getConsignments();
        $itemMeta = [];
        foreach ($consignments as $c) {
            foreach ($c->items as $item) {
                $itemMeta[$item->id] = [
                    'name'       => $item->product?->name ?? $item->product_name,
                    'unit_price' => $item->unit_price,
                    'item_id'    => $item->id,
                ];
            }
        }

        $soldMap = [];
        $paidMap = [];
        $payments = ConsignmentPayment::where('wholesale_request_id', $this->record->id)->get();
        foreach ($payments as $pay) {
            $items = is_string($pay->items_sold) ? json_decode($pay->items_sold, true) : $pay->items_sold;
            if (! is_array($items)) continue;
            foreach ($items as $s) {
                $id = (int)($s['consignment_item_id'] ?? 0);
                $soldMap[$id] = ($soldMap[$id] ?? 0) + (int)($s['qty_sold'] ?? 0);
                $paidMap[$id] = ($paidMap[$id] ?? 0) + (int)($s['qty_paid'] ?? 0);
            }
        }

        $pending = [];
        foreach ($itemMeta as $id => $meta) {
            $sold = $soldMap[$id] ?? 0;
            $paid = $paidMap[$id] ?? 0;
            $diff = $sold - $paid;
            if ($diff > 0) {
                $name = $meta['name'];
                if (! isset($pending[$name])) {
                    $pending[$name] = ['item_id' => $id, 'qty' => 0, 'unit_price' => $meta['unit_price']];
                }
                $pending[$name]['qty'] += $diff;
            }
        }

        return $pending; // ['Botella Citrino' => ['item_id'=>X,'qty'=>1,'unit_price'=>48000]]
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('registrar_pago')
                ->label('💳 Registrar pago')
                ->color('success')
                ->form(fn() => $this->paymentForm(
                    collect($this->getProductStock())->mapWithKeys(
                        fn($v, $name) => [$name => $name . ' (stock: ' . $v['stock'] . ' u.)']
                    )->toArray()
                ))
                ->action(function (array $data) {
                    $stock = $this->getProductStock();
                    ConsignmentPayment::create([
                        'wholesale_request_id' => $this->record->id,
                        'consignment_id'       => null,
                        'amount'               => $data['amount'],
                        'notes'                => $data['notes'] ?? null,
                        'receipt'              => $data['receipt'] ?? null,
                        'items_sold'           => $this->resolveItemsSold($data['items_sold'] ?? [], $stock),
                    ]);
                    Notification::make()->title('Pago registrado')->success()->send();
                }),

            Action::make('imputar_pendiente')
                ->label('⏳ Imputar pendiente')
                ->color('warning')
                ->form(function () {
                    $pending = $this->getPendingUnits();
                    if (empty($pending)) return [
                        Forms\Components\Placeholder::make('no_pending')
                            ->label('')->content('No hay unidades pendientes de cobro. ✓'),
                    ];

                    $options = collect($pending)->mapWithKeys(
                        fn($v, $name) => [$name => $name . ' — ' . $v['qty'] . ' u. pendiente' . ($v['qty'] > 1 ? 's' : '')]
                    )->toArray();

                    return [
                        Forms\Components\TextInput::make('amount')
                            ->label('Monto cobrado ($)')
                            ->numeric()->required()->prefix('$'),

                        Forms\Components\FileUpload::make('receipt')
                            ->label('Comprobante')
                            ->acceptedFileTypes(['image/jpeg','image/png','image/webp','application/pdf'])
                            ->directory('consignment-receipts')
                            ->maxSize(5120),

                        Forms\Components\Textarea::make('notes')->label('Notas (opcional)')->rows(2),

                        Forms\Components\Repeater::make('items')
                            ->label('Unidades pendientes que cobra ahora')
                            ->schema([
                                Forms\Components\Select::make('product_name')
                                    ->label('Producto pendiente')
                                    ->options($options)
                                    ->required()->searchable(),
                                Forms\Components\TextInput::make('qty_paid')
                                    ->label('Unidades que paga')
                                    ->numeric()->required()->minValue(1)->default(1),
                            ])
                            ->columns(2)
                            ->addActionLabel('+ Agregar')
                            ->defaultItems(1),
                    ];
                })
                ->action(function (array $data) {
                    $pending = $this->getPendingUnits();
                    $itemsSold = collect($data['items'] ?? [])->map(function ($row) use ($pending) {
                        $name   = $row['product_name'];
                        $itemId = $pending[$name]['item_id'] ?? null;
                        return [
                            'consignment_item_id' => $itemId,
                            'qty_sold'            => 0,
                            'qty_paid'            => (int)($row['qty_paid'] ?? 0),
                        ];
                    })->filter(fn($r) => $r['consignment_item_id'])->values()->toArray();

                    ConsignmentPayment::create([
                        'wholesale_request_id' => $this->record->id,
                        'consignment_id'       => null,
                        'amount'               => $data['amount'],
                        'notes'                => $data['notes'] ?? null,
                        'receipt'              => $data['receipt'] ?? null,
                        'items_sold'           => $itemsSold,
                    ]);
                    Notification::make()->title('Pendiente imputado')->success()->send();
                }),

            Action::make('nueva_entrega')
                ->label('+ Nueva entrega')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->form([
                    Forms\Components\DatePicker::make('delivery_date')
                        ->label('Fecha de entrega')->default(now())->required(),
                    Forms\Components\Select::make('status')
                        ->label('Estado')
                        ->options(['active' => 'Activa', 'closed' => 'Cerrada'])
                        ->default('active')->required(),
                    Forms\Components\Textarea::make('notes')->label('Notas')->rows(2),
                    Forms\Components\Repeater::make('items')
                        ->label('Productos a entregar')
                        ->schema([
                            Forms\Components\Select::make('product_id')
                                ->label('Producto')
                                ->options(
                                    Product::with('category')->orderBy('name')->get()
                                        ->groupBy(fn($p) => $p->category?->name ?? 'Sin categoría')
                                        ->map(fn($g) => $g->pluck('name', 'id'))->toArray()
                                )
                                ->searchable()->required()->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state && $p = Product::find($state)) {
                                        $set('unit_price', $p->price_wholesale);
                                        $set('product_name', $p->name);
                                    }
                                }),
                            Forms\Components\TextInput::make('quantity')
                                ->label('Cantidad')->numeric()->required()->default(1),
                            Forms\Components\TextInput::make('unit_price')
                                ->label('Precio unit. ($)')->numeric()->required()->prefix('$'),
                            Forms\Components\Hidden::make('product_name'),
                        ])
                        ->columns(3)->addActionLabel('+ Agregar producto')->defaultItems(1),
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

                    // Email automático al mayorista
                    try {
                        LaravelNotification::route('mail', $this->record->email)
                            ->notify(new NewConsignmentDeliveryNotification($consignment, $this->record));
                    } catch (\Exception $e) {
                        // No interrumpir si falla el mail
                    }

                    Notification::make()->title('Entrega registrada — email enviado al local')->success()->send();
                }),
        ];
    }

    /** Acciones inline por fila en el blade (editar/borrar pago) */
    public function editPayment(int $paymentId): void
    {
        // handled via mountAction in blade
    }

    public function deletePayment(int $paymentId): void
    {
        ConsignmentPayment::where('id', $paymentId)
            ->where('wholesale_request_id', $this->record->id)
            ->delete();
        Notification::make()->title('Pago eliminado')->success()->send();
    }
}
