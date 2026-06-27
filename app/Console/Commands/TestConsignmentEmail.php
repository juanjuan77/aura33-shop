<?php

namespace App\Console\Commands;

use App\Models\Consignment;
use App\Models\ConsignmentItem;
use App\Models\WholesaleRequest;
use App\Notifications\NewConsignmentDeliveryNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class TestConsignmentEmail extends Command
{
    protected $signature   = 'test:consignment-email {email}';
    protected $description = 'Envía email de prueba de nueva entrega';

    public function handle(): void
    {
        $email      = $this->argument('email');
        $wholesaler = WholesaleRequest::where('email', $email)->first()
            ?? WholesaleRequest::where('status', 'approved')->first();

        if (! $wholesaler) {
            $this->error('No se encontró mayorista aprobado.');
            return;
        }

        // Consignacion falsa en memoria (sin guardar)
        $consignment = new Consignment([
            'wholesale_request_id' => $wholesaler->id,
            'delivery_date'        => now(),
            'status'               => 'active',
            'notes'                => 'Entrega de prueba — podés ignorar este email.',
        ]);
        $consignment->id = 0;

        // Items falsos
        $consignment->setRelation('items', collect([
            tap(new ConsignmentItem([
                'product_name' => 'Botella Fluorita Verde',
                'quantity'     => 6,
                'unit_price'   => 48000,
            ]), fn($i) => $i->setRelation('product', null)),
            tap(new ConsignmentItem([
                'product_name' => 'Botella Amatista',
                'quantity'     => 4,
                'unit_price'   => 48000,
            ]), fn($i) => $i->setRelation('product', null)),
        ]));

        Notification::route('mail', $email)
            ->notify(new NewConsignmentDeliveryNotification($consignment, $wholesaler));

        $this->info("Email de prueba enviado a {$email}");
    }
}
