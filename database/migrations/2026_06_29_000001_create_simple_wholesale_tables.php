<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wholesale_deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wholesale_request_id');
            $table->unsignedInteger('quantity');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('wholesale_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wholesale_request_id');
            $table->string('product_name');
            $table->unsignedInteger('quantity');
            $table->decimal('amount', 10, 2);
            $table->string('receipt')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wholesale_payments');
        Schema::dropIfExists('wholesale_deliveries');
    }
};
