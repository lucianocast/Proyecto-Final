<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insumo_receta', function (Blueprint $table) {
            $table->unsignedBigInteger('receta_id');
            $table->unsignedBigInteger('insumo_id');
            $table->decimal('cantidad', 16, 6);
            $table->timestamps();

            $table->primary(['receta_id', 'insumo_id']);

            $table->foreign('receta_id')->references('id')->on('recetas')->onDelete('cascade');
            $table->foreign('insumo_id')->references('id')->on('insumos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insumo_receta');
    }
};
