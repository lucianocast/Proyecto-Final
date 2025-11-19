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
        Schema::table('recetas', function (Blueprint $table) {
            $table->text('descripcion')->nullable()->after('instrucciones');
            $table->integer('porciones')->default(1)->after('descripcion');
            $table->string('tiempo_preparacion')->nullable()->after('porciones');
            $table->boolean('activo')->default(true)->after('tiempo_preparacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recetas', function (Blueprint $table) {
            $table->dropColumn(['descripcion', 'porciones', 'tiempo_preparacion', 'activo']);
        });
    }
};
