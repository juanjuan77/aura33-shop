<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Entregas en consignación
        Schema::create('consignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wholesale_request_id');
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->timestamps();
        });

        // Productos entregados por consignación
        Schema::create('consignment_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consignment_id');
            $table->string('product_name');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->timestamps();
        });

        // Reportes de ventas del local
        Schema::create('consignment_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wholesale_request_id');
            $table->unsignedBigInteger('consignment_id')->nullable();
            $table->text('description');
            $table->decimal('amount', 10, 2);
            $table->string('receipt')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $table->timestamp('confirmed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        // Pagos recibidos
        Schema::create('consignment_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wholesale_request_id');
            $table->decimal('amount', 10, 2);
            $table->string('receipt')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consignment_payments');
        Schema::dropIfExists('consignment_reports');
        Schema::dropIfExists('consignment_items');
        Schema::dropIfExists('consignments');
    }
};
