<?php

namespace App\Http\Controllers;

use App\Models\ConsignmentItem;
use App\Models\ConsignmentPayment;
use App\Models\WholesaleRequest;
use Illuminate\Http\Request;

class ConsignmentExportController extends Controller
{
    public function pdf(Request $request)
    {
        $wholesalerId = $request->integer('wholesaler');
        $wholesaler   = WholesaleRequest::findOrFail($wholesalerId);
        $data         = $this->buildReport($wholesalerId);

        return view('exports.consignment-pdf', compact('wholesaler', 'data'));
    }

    public function csv(Request $request)
    {
        $wholesalerId = $request->integer('wholesaler');
        $wholesaler   = WholesaleRequest::findOrFail($wholesalerId);
        $data         = $this->buildReport($wholesalerId);

        $filename = 'consignacion-' . str_replace(' ', '-', strtolower($wholesaler->business_name)) . '-' . now()->format('Y-m-d') . '.csv';

        $rows   = [];
        $rows[] = ['Producto', 'Categoría', 'Entregadas', 'Vendidas', 'En stock', 'Pagas', 'Debe (u.)', 'Monto debe ($)'];
        foreach ($data['report'] as $r) {
            $rows[] = [
                $r['product_name'],
                $r['category'],
                $r['delivered'],
                $r['sold'],
                $r['stock'],
                $r['paid_qty'],
                $r['debe'],
                $r['debe_amount'],
            ];
        }
        $rows[] = [];
        $rows[] = ['TOTAL', '', $data['totals']['delivered'], $data['totals']['sold'], $data['totals']['stock'], $data['totals']['paid_qty'], $data['totals']['debe'], $data['totals']['debe_amount']];

        $output = fopen('php://temp', 'r+');
        foreach ($rows as $row) {
            fputcsv($output, $row, ';');
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    protected function buildReport(int $wholesalerId): array
    {
        $items = ConsignmentItem::with(['product.category'])
            ->whereHas('consignment', fn($q) => $q->where('wholesale_request_id', $wholesalerId))
            ->get();

        $payments    = ConsignmentPayment::where('wholesale_request_id', $wholesalerId)->get();
        $itemIds     = $items->pluck('id')->flip();
        $soldMap     = [];
        $paidMap     = [];

        foreach ($payments as $pay) {
            $soldItems = is_string($pay->items_sold) ? json_decode($pay->items_sold, true) : $pay->items_sold;
            if (! is_array($soldItems)) continue;
            foreach ($soldItems as $s) {
                $id = (int)($s['consignment_item_id'] ?? 0);
                if ($itemIds->has($id)) {
                    $soldMap[$id] = ($soldMap[$id] ?? 0) + (int)($s['qty_sold'] ?? 0);
                    $paidMap[$id] = ($paidMap[$id] ?? 0) + (int)($s['qty_paid'] ?? 0);
                }
            }
        }

        $report = $items->groupBy('product_id')->map(function ($rows) use ($soldMap, $paidMap) {
            $first     = $rows->first();
            $delivered = $rows->sum('quantity');
            $sold      = $rows->sum(fn($r) => $soldMap[$r->id] ?? 0);
            $paid      = $rows->sum(fn($r) => $paidMap[$r->id] ?? 0);
            $stock     = max(0, $delivered - $sold);
            $debe      = max(0, $sold - $paid);
            return [
                'product_name' => $first->product?->name ?? $first->product_name ?? '?',
                'category'     => $first->product?->category?->name ?? 'Sin categoría',
                'unit_price'   => $first->unit_price,
                'delivered'    => $delivered,
                'sold'         => $sold,
                'paid_qty'     => $paid,
                'stock'        => $stock,
                'debe'         => $debe,
                'debe_amount'  => $debe * $first->unit_price,
            ];
        })->values()->sortBy('category')->values();

        $totals = [
            'delivered'   => $report->sum('delivered'),
            'sold'        => $report->sum('sold'),
            'stock'       => $report->sum('stock'),
            'paid_qty'    => $report->sum('paid_qty'),
            'debe'        => $report->sum('debe'),
            'debe_amount' => $report->sum('debe_amount'),
            'total_paid'  => $payments->sum('amount'),
            'total_entregado' => $items->sum(fn($i) => $i->quantity * $i->unit_price),
        ];

        return compact('report', 'totals');
    }
}
