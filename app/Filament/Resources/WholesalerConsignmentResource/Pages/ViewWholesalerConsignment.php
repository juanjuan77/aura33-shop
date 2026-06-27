<?php

namespace App\Filament\Resources\WholesalerConsignmentResource\Pages;

use App\Filament\Resources\WholesalerConsignmentResource;
use App\Models\Consignment;
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
    public bool $showNewDelivery = false;
    public array $newDelivery = [];

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

    protected function getHeaderActions(): array
    {
        return [
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
