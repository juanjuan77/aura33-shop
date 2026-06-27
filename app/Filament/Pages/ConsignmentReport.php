<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\Consignment;
use App\Models\ConsignmentItem;
use App\Models\ConsignmentPayment;
use App\Models\WholesaleRequest;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class ConsignmentReport extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Informe Consignación';
    protected static ?string $title           = 'Informe de Consignación';
    protected static ?int    $navigationSort  = 7;
    protected static string  $view            = 'filament.pages.consignment-report';

    public ?int    $selectedWholesaler = null;
    public ?int    $selectedCategory   = null;
    public ?int    $detailProductId    = null;
    public bool    $showDetail         = false;

    public function getWholesalers(): Collection
    {
        return WholesaleRequest::where('status', 'approved')->orderBy('business_name')->get();
    }

    public function getCategories(): Collection
    {
        return Category::orderBy('name')->get();
    }

    public function openDetail(int $productId): void
    {
        $this->detailProductId = $productId;
        $this->showDetail      = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail      = false;
        $this->detailProductId = null;
    }

    public function getProductDetail(): Collection
    {
        if (! $this->selectedWholesaler || ! $this->detailProductId) return collect();

        return Consignment::where('wholesale_request_id', $this->selectedWholesaler)
            ->with(['items' => fn($q) => $q->where('product_id', $this->detailProductId)->with('product'),
                    'payments'])
            ->orderBy('delivery_date')
            ->get()
            ->filter(fn($c) => $c->items->isNotEmpty())
            ->map(function ($c) {
                $item = $c->items->first();
                $sold = 0; $paid = 0;
                foreach ($c->payments as $pay) {
                    $soldItems = is_string($pay->items_sold) ? json_decode($pay->items_sold, true) : $pay->items_sold;
                    if (! is_array($soldItems)) continue;
                    foreach ($soldItems as $s) {
                        if ((int)($s['consignment_item_id'] ?? 0) === $item->id) {
                            $sold += (int)($s['qty_sold'] ?? 0);
                            $paid += (int)($s['qty_paid'] ?? $s['qty_sold'] ?? 0);
                        }
                    }
                }
                return [
                    'delivery_date' => $c->delivery_date?->format('d/m/Y') ?? $c->created_at->format('d/m/Y'),
                    'status'        => $c->status,
                    'quantity'      => $item->quantity,
                    'unit_price'    => $item->unit_price,
                    'sold'          => $sold,
                    'paid'          => $paid,
                    'debe'          => max(0, $sold - $paid),
                    'stock'         => max(0, $item->quantity - $sold),
                    'subtotal'      => $item->quantity * $item->unit_price,
                    'notes'         => $c->notes,
                ];
            })->values();
    }

    public function getReportData(): Collection
    {
        if (! $this->selectedWholesaler) return collect();

        $query = ConsignmentItem::with(['product.category'])
            ->whereHas('consignment', fn($q) => $q->where('wholesale_request_id', $this->selectedWholesaler));

        if ($this->selectedCategory) {
            $query->whereHas('product', fn($q) => $q->where('category_id', $this->selectedCategory));
        }

        $items = $query->get();

        // Cargar TODOS los pagos del mayorista (con o sin consignment_id)
        $allPayments = ConsignmentPayment::where('wholesale_request_id', $this->selectedWholesaler)->get();
        $itemIds = $items->pluck('id')->flip(); // id => index para búsqueda O(1)

        // Pre-calcular qty_sold por item_id desde todos los pagos
        $soldMap = [];
        foreach ($allPayments as $payment) {
            $soldItems = is_string($payment->items_sold)
                ? json_decode($payment->items_sold, true)
                : $payment->items_sold;
            if (! is_array($soldItems)) continue;
            foreach ($soldItems as $s) {
                $id = (int)($s['consignment_item_id'] ?? 0);
                if ($itemIds->has($id)) {
                    $soldMap[$id] = ($soldMap[$id] ?? 0) + (int)($s['qty_sold'] ?? 0);
                }
            }
        }

        // Group by product
        return $items->groupBy('product_id')->map(function ($rows) use ($soldMap) {
            $first     = $rows->first();
            $product   = $first->product;
            $category  = $product?->category?->name ?? 'Sin categoría';
            $delivered = $rows->sum('quantity');
            $unitPrice = $first->unit_price;
            $allSold   = $rows->sum(fn($r) => $soldMap[$r->id] ?? 0);
            $stock     = max(0, $delivered - $allSold);

            return [
                'product_id'   => $first->product_id,
                'product_name' => $product?->name ?? $first->product_name ?? '?',
                'category'     => $category,
                'unit_price'   => $unitPrice,
                'delivered'    => $delivered,
                'sold'         => $allSold,
                'paid_qty'     => 0,
                'stock'        => $stock,
                'debe'         => 0,
                'debe_amount'  => 0,
            ];
        })->values()->sortBy('category');
    }
}
