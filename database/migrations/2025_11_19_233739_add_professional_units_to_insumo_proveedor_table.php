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
        Schema::table('insumo_proveedor', function (Blueprint $table) {
            // Eliminar columnas antiguas
            $table->dropColumn(['unidad_de_compra', 'factor_de_conversion']);
        });

        Schema::table('insumo_proveedor', function (Blueprint $table) {
            // Agregar nuevas columnas profesionalizadas
            $table->string('unidad_compra')->nullable()->after('precio');
            $table->decimal('cantidad_por_bulto', 10, 2)->default(1)->after('unidad_compra');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insumo_proveedor', function (Blueprint $table) {
            // Eliminar nuevas columnas
            $table->dropColumn(['unidad_compra', 'cantidad_por_bulto']);
        });

        Schema::table('insumo_proveedor', function (Blueprint $table) {
            // Restaurar columnas originales
            $table->string('unidad_de_compra')->after('precio');
            $table->decimal('factor_de_conversion', 16, 6)->default(1)->after('unidad_de_compra');
        });
    }
};
