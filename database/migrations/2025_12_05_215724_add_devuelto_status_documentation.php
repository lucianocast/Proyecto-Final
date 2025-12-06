<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * NOTA: Esta migración es solo documentación.
     * El campo 'status' en la tabla 'pedidos' ya es de tipo string,
     * por lo que soporta el nuevo valor 'devuelto' sin modificación estructural.
     * 
     * Estados válidos de pedidos:
     * - pendiente
     * - en_produccion
     * - listo
     * - entregado
     * - cancelado
     * - devuelto (UC-13: Nuevo estado para devoluciones/reintegros)
     */
    public function up(): void
    {
        // No se requiere modificación de estructura.
        // El campo status ya es string y acepta cualquier valor.
        
        // Opcional: Si deseas agregar un comentario a la columna en PostgreSQL:
        // DB::statement("COMMENT ON COLUMN pedidos.status IS 'Estados: pendiente, en_produccion, listo, entregado, cancelado, devuelto'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No se requiere reversión.
        // El valor 'devuelto' simplemente dejará de usarse.
    }
};
