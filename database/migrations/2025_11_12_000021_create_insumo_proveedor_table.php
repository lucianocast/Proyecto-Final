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
        Schema::create('insumo_proveedor', function (Blueprint $table) {
            $table->unsignedBigInteger('insumo_id');
            $table->unsignedBigInteger('proveedor_id');
            $table->decimal('precio', 12, 2);
            $table->string('unidad_de_compra');
            $table->decimal('factor_de_conversion', 16, 6)->default(1);
            $table->integer('tiempo_entrega_dias')->nullable();
            $table->timestamps();

            $table->primary(['insumo_id', 'proveedor_id']);

            $table->foreign('insumo_id')->references('id')->on('insumos')->onDelete('cascade');
            $table->foreign('proveedor_id')->references('id')->on('proveedores')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumo_proveedor');
    }
};
