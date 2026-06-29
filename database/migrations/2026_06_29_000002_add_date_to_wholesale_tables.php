<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wholesale_deliveries', function (Blueprint $table) {
            $table->date('date')->nullable()->after('wholesale_request_id');
        });

        Schema::table('wholesale_payments', function (Blueprint $table) {
            $table->date('date')->nullable()->after('wholesale_request_id');
        });
    }

    public function down(): void
    {
        Schema::table('wholesale_deliveries', fn($t) => $t->dropColumn('date'));
        Schema::table('wholesale_payments',   fn($t) => $t->dropColumn('date'));
    }
};
