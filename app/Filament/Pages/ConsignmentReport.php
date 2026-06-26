<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\ConsignmentItem;
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

    public ?int $selectedWholesaler = null;
    public ?int $selectedCategory   = null;

    public function getWholesalers(): Collection
    {
        return WholesaleRequest::where('status', 'approved')->orderBy('business_name')->get();
    }

    public function getCategories(): Collection
    {
        return Category::orderBy('name')->get();
    }

    public function getReportData(): Collection
    {
        if (! $this->selectedWholesaler) return collect();

        $query = ConsignmentItem::with(['product.category', 'consignment.payments'])
            ->whereHas('consignment', fn($q) => $q->where('wholesale_request_id', $this->selectedWholesaler));

        if ($this->selectedCategory) {
            $query->whereHas('product', fn($q) => $q->where('category_id', $this->selectedCategory));
        }

        $items = $query->get();

        // Group by product
        return $items->groupBy('product_id')->map(function ($rows) {
            $first      = $rows->first();
            $product    = $first->product;
            $category   = $product?->category?->name ?? 'Sin categoría';
            $delivered  = $rows->sum('quantity');

            // All payments for these consignment items
            $allSold = 0;
            $allPaid = 0;

            foreach ($rows as $row) {
                foreach ($row->consignment->payments as $payment) {
                    $soldItems = is_string($payment->items_sold)
                        ? json_decode($payment->items_sold, true)
                        : $payment->items_sold;
                    if (! is_array($soldItems)) continue;
                    foreach ($soldItems as $s) {
                        if ((int)($s['consignment_item_id'] ?? 0) === $row->id) {
                            $allSold += (int)($s['qty_sold'] ?? 0);
                            $allPaid += (int)($s['qty_paid'] ?? $s['qty_sold'] ?? 0);
                        }
                    }
                }
            }

            $stock = max(0, $delivered - $allSold);
            $debe  = max(0, $allSold - $allPaid);

            return [
                'product_id'   => $first->product_id,
                'product_name' => $product?->name ?? $first->product_name ?? '?',
                'category'     => $category,
                'unit_price'   => $first->unit_price,
                'delivered'    => $delivered,
                'sold'         => $allSold,
                'paid_qty'     => $allPaid,
                'stock'        => $stock,
                'debe'         => $debe,
                'debe_amount'  => $debe * $first->unit_price,
            ];
        })->values()->sortBy('category');
    }
}
