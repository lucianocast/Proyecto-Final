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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            
            // Clave foránea al pedido
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            
            // Monto del pago (decimal para precisión exacta)
            $table->decimal('monto', 10, 2);
            
            // Método de pago
            $table->string('metodo', 50);
            
            // Estado del pago
            $table->string('estado', 50)->default('pendiente');
            
            // Referencia externa (ej. ID de transacción de Mercado Pago)
            $table->string('referencia_externa', 255)->nullable();
            
            // Fecha de pago
            $table->timestamp('fecha_pago')->nullable();
            
            // Índices para optimizar consultas
            $table->index('pedido_id');
            $table->index('estado');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
