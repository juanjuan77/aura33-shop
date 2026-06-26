<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Link consignment_items to product catalog
        Schema::table('consignment_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->after('consignment_id');
        });

        // Add delivery date to consignments
        Schema::table('consignments', function (Blueprint $table) {
            $table->date('delivery_date')->nullable()->after('status');
        });

        // Add qty_paid per item sold in payments
        // items_sold JSON already exists, we'll handle qty_paid inside the JSON
    }

    public function down(): void
    {
        Schema::table('consignment_items', function (Blueprint $table) {
            $table->dropColumn('product_id');
        });
        Schema::table('consignments', function (Blueprint $table) {
            $table->dropColumn('delivery_date');
        });
    }
};
