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
        Schema::create('wholesale_requests', function (Blueprint $table) {
            $table->id();
            // Datos del solicitante
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('business_name');           // nombre del negocio / emprendimiento
            $table->string('cuit')->nullable();        // CUIT / DNI
            $table->string('city');
            $table->string('province')->default('Santa Fe');
            $table->string('business_type')->nullable(); // tienda física, online, terapeuta, etc.
            $table->text('notes')->nullable();         // cómo conoció AURA33, qué espera, etc.
            // Estado
            $table->string('status')->default('pending'); // pending | approved | rejected
            $table->text('admin_notes')->nullable();   // nota interna del admin
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wholesale_requests');
    }
};
