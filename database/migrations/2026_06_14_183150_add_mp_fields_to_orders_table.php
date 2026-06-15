<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('mp_preference_id')->nullable()->after('transfer_receipt');
            $table->string('mp_payment_id')->nullable()->after('mp_preference_id');
            $table->decimal('mp_surcharge', 10, 2)->default(0)->after('mp_payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['mp_preference_id', 'mp_payment_id', 'mp_surcharge']);
        });
    }
};
