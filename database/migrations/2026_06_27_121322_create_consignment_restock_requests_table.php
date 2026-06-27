<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consignment_restock_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wholesale_request_id');
            $table->json('items'); // [{product_id, product_name, category, quantity}]
            $table->enum('status', ['pending', 'seen', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consignment_restock_requests');
    }
};
