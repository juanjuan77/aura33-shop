<?php

namespace Database\Seeders;

use App\Models\Consignment;
use App\Models\ConsignmentItem;
use App\Models\ConsignmentPayment;
use App\Models\Product;
use App\Models\WholesaleRequest;
use Illuminate\Database\Seeder;

class ConsignmentDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar todo
        \DB::table('consignment_payments')->truncate();
        \DB::table('consignment_reports')->truncate();
        \DB::table('consignment_items')->truncate();
        \DB::table('consignments')->truncate();

        $wholesaler = WholesaleRequest::where('status', 'approved')->first();
        if (! $wholesaler) {
            $this->command->error('No hay mayoristas aprobados.');
            return;
        }

        // Tomar 6 productos del catálogo
        $products = Product::whereNotNull('price_wholesale')->take(8)->get();
        if ($products->count() < 3) {
            $this->command->error('Necesitás al menos 3 productos en el catálogo.');
            return;
        }

        $wid = $wholesaler->id;

        // ── Entrega 1: Marzo — 4 productos ─────────────────────────
        $c1 = Consignment::create([
            'wholesale_request_id' => $wid,
            'status'        => 'active',
            'delivery_date' => '2026-03-10',
            'notes'         => 'Primera entrega del año',
        ]);
        $p0 = $products[0]; $p1 = $products[1]; $p2 = $products[2]; $p3 = $products[3] ?? $products[0];
        $i1a = ConsignmentItem::create(['consignment_id'=>$c1->id,'product_id'=>$p0->id,'product_name'=>$p0->name,'quantity'=>5,'unit_price'=>$p0->price_wholesale]);
        $i1b = ConsignmentItem::create(['consignment_id'=>$c1->id,'product_id'=>$p1->id,'product_name'=>$p1->name,'quantity'=>4,'unit_price'=>$p1->price_wholesale]);
        $i1c = ConsignmentItem::create(['consignment_id'=>$c1->id,'product_id'=>$p2->id,'product_name'=>$p2->name,'quantity'=>3,'unit_price'=>$p2->price_wholesale]);
        $i1d = ConsignmentItem::create(['consignment_id'=>$c1->id,'product_id'=>$p3->id,'product_name'=>$p3->name,'quantity'=>6,'unit_price'=>$p3->price_wholesale]);

        // Pago 1 de entrega 1 — vendió 3 del p0, 2 del p1, pagó todo
        ConsignmentPayment::create([
            'wholesale_request_id' => $wid,
            'consignment_id' => $c1->id,
            'amount'    => ($p0->price_wholesale * 3) + ($p1->price_wholesale * 2),
            'notes'     => 'Transferencia MP abril',
            'items_sold'=> json_encode([
                ['consignment_item_id'=>$i1a->id,'qty_sold'=>3,'qty_paid'=>3],
                ['consignment_item_id'=>$i1b->id,'qty_sold'=>2,'qty_paid'=>2],
            ]),
        ]);

        // Pago 2 de entrega 1 — vendió 2 del p2, solo pagó 1
        ConsignmentPayment::create([
            'wholesale_request_id' => $wid,
            'consignment_id' => $c1->id,
            'amount'    => $p2->price_wholesale * 1,
            'notes'     => 'Pago parcial mayo',
            'items_sold'=> json_encode([
                ['consignment_item_id'=>$i1c->id,'qty_sold'=>2,'qty_paid'=>1],
            ]),
        ]);

        // ── Entrega 2: Abril — 3 productos ─────────────────────────
        $p4 = $products[4] ?? $products[1]; $p5 = $products[5] ?? $products[2];
        $c2 = Consignment::create([
            'wholesale_request_id' => $wid,
            'status'        => 'active',
            'delivery_date' => '2026-04-05',
            'notes'         => 'Refuerzo stock primavera',
        ]);
        $i2a = ConsignmentItem::create(['consignment_id'=>$c2->id,'product_id'=>$p4->id,'product_name'=>$p4->name,'quantity'=>8,'unit_price'=>$p4->price_wholesale]);
        $i2b = ConsignmentItem::create(['consignment_id'=>$c2->id,'product_id'=>$p5->id,'product_name'=>$p5->name,'quantity'=>5,'unit_price'=>$p5->price_wholesale]);
        $i2c = ConsignmentItem::create(['consignment_id'=>$c2->id,'product_id'=>$p0->id,'product_name'=>$p0->name,'quantity'=>4,'unit_price'=>$p0->price_wholesale]);

        // Pago — vendió todo p5, pagó todo; vendió 3 del p4, pagó 2
        ConsignmentPayment::create([
            'wholesale_request_id' => $wid,
            'consignment_id' => $c2->id,
            'amount'    => ($p5->price_wholesale * 5) + ($p4->price_wholesale * 2),
            'notes'     => 'Pago mayo transferencia',
            'items_sold'=> json_encode([
                ['consignment_item_id'=>$i2a->id,'qty_sold'=>3,'qty_paid'=>2],
                ['consignment_item_id'=>$i2b->id,'qty_sold'=>5,'qty_paid'=>5],
            ]),
        ]);

        // ── Entrega 3: Mayo — 2 productos ──────────────────────────
        $c3 = Consignment::create([
            'wholesale_request_id' => $wid,
            'status'        => 'active',
            'delivery_date' => '2026-05-15',
        ]);
        $p6 = $products[6] ?? $products[3];
        $i3a = ConsignmentItem::create(['consignment_id'=>$c3->id,'product_id'=>$p6->id,'product_name'=>$p6->name,'quantity'=>6,'unit_price'=>$p6->price_wholesale]);
        $i3b = ConsignmentItem::create(['consignment_id'=>$c3->id,'product_id'=>$p1->id,'product_name'=>$p1->name,'quantity'=>4,'unit_price'=>$p1->price_wholesale]);

        // Solo vendió 2, no pagó nada aún
        ConsignmentPayment::create([
            'wholesale_request_id' => $wid,
            'consignment_id' => $c3->id,
            'amount'    => 0,
            'notes'     => 'Reportó ventas, pago pendiente',
            'items_sold'=> json_encode([
                ['consignment_item_id'=>$i3a->id,'qty_sold'=>2,'qty_paid'=>0],
            ]),
        ]);

        // ── Entrega 4: Junio ────────────────────────────────────────
        $c4 = Consignment::create([
            'wholesale_request_id' => $wid,
            'status'        => 'active',
            'delivery_date' => '2026-06-01',
            'notes'         => 'Pedido especial junio',
        ]);
        $i4a = ConsignmentItem::create(['consignment_id'=>$c4->id,'product_id'=>$p0->id,'product_name'=>$p0->name,'quantity'=>3,'unit_price'=>$p0->price_wholesale]);
        $i4b = ConsignmentItem::create(['consignment_id'=>$c4->id,'product_id'=>$p2->id,'product_name'=>$p2->name,'quantity'=>5,'unit_price'=>$p2->price_wholesale]);
        // Sin pagos aún

        // ── Entrega 5: Junio más reciente ──────────────────────────
        $c5 = Consignment::create([
            'wholesale_request_id' => $wid,
            'status'        => 'active',
            'delivery_date' => '2026-06-20',
        ]);
        $p7 = $products[7] ?? $products[4];
        $i5a = ConsignmentItem::create(['consignment_id'=>$c5->id,'product_id'=>$p7->id,'product_name'=>$p7->name,'quantity'=>10,'unit_price'=>$p7->price_wholesale]);
        $i5b = ConsignmentItem::create(['consignment_id'=>$c5->id,'product_id'=>$p5->id,'product_name'=>$p5->name,'quantity'=>4,'unit_price'=>$p5->price_wholesale]);

        ConsignmentPayment::create([
            'wholesale_request_id' => $wid,
            'consignment_id' => $c5->id,
            'amount'    => $p7->price_wholesale * 3,
            'notes'     => 'Primer pago junio',
            'items_sold'=> json_encode([
                ['consignment_item_id'=>$i5a->id,'qty_sold'=>4,'qty_paid'=>3],
            ]),
        ]);

        // ── Entrega 6: cerrada ─────────────────────────────────────
        $c6 = Consignment::create([
            'wholesale_request_id' => $wid,
            'status'        => 'closed',
            'delivery_date' => '2026-01-20',
            'notes'         => 'Entrega cerrada y saldada',
        ]);
        $i6a = ConsignmentItem::create(['consignment_id'=>$c6->id,'product_id'=>$p1->id,'product_name'=>$p1->name,'quantity'=>3,'unit_price'=>$p1->price_wholesale]);
        ConsignmentPayment::create([
            'wholesale_request_id' => $wid,
            'consignment_id' => $c6->id,
            'amount'    => $p1->price_wholesale * 3,
            'notes'     => 'Saldado completo',
            'items_sold'=> json_encode([
                ['consignment_item_id'=>$i6a->id,'qty_sold'=>3,'qty_paid'=>3],
            ]),
        ]);

        $this->command->info("✓ 6 consignaciones creadas para: {$wholesaler->business_name}");
    }
}
