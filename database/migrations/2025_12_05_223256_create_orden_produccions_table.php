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
        Schema::create('orden_produccions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receta_id')->nullable()->constrained('recetas')->nullOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('cantidad_a_producir')->default(1);
            $table->integer('cantidad_producida')->nullable();
            $table->string('estado')->default('pendiente'); // pendiente, en_proceso, terminada, cancelada
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_limite')->nullable();
            $table->date('fecha_finalizacion')->nullable();
            $table->json('insumos_estimados')->nullable(); // JSON con insumos requeridos
            $table->json('insumos_consumidos')->nullable(); // JSON con insumos realmente consumidos
            $table->text('observaciones')->nullable();
            $table->decimal('costo_total', 12, 2)->nullable();
            $table->timestamps();
        });

        // Tabla pivote para vincular OPs con Pedidos
        Schema::create('orden_produccion_pedido', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_produccion_id')->constrained('orden_produccions')->onDelete('cascade');
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_produccion_pedido');
        Schema::dropIfExists('orden_produccions');
    }
};
