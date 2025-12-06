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
        Schema::create('movimiento_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insumo_id')->constrained('insumos')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('tipo', ['entrada', 'salida', 'ajuste']);
            $table->decimal('cantidad', 14, 4);
            $table->decimal('cantidad_anterior', 14, 4);
            $table->decimal('cantidad_nueva', 14, 4);
            $table->string('referencia')->nullable(); // ID de OP, OC, etc.
            $table->string('tipo_referencia')->nullable(); // 'orden_produccion', 'orden_compra', etc.
            $table->text('justificacion')->nullable();
            $table->timestamps();

            $table->index(['insumo_id', 'created_at']);
            $table->index('tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimiento_stocks');
    }
};
