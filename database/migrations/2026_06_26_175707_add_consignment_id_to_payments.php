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
        Schema::table('consignment_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('consignment_id')->nullable()->after('wholesale_request_id');
        });
    }

    public function down(): void
    {
        Schema::table('consignment_payments', function (Blueprint $table) {
            $table->dropColumn('consignment_id');
        });
    }
};
