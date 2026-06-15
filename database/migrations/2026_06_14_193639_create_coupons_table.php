<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('discount_percent', 5, 2);
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('uses_count')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('coupon_code')->nullable()->after('notes');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('coupon_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['coupon_code', 'discount_amount']);
        });
    }
};
