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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            
            // Usuario que realizó la acción
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Tipo de acción realizada
            $table->string('action', 50); // created, updated, deleted, cancelled, etc.
            
            // Modelo afectado
            $table->string('auditable_type'); // App\Models\Pedido, etc.
            $table->unsignedBigInteger('auditable_id'); // ID del registro
            
            // Datos antes y después del cambio
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            
            // Justificación (para cancelaciones, anulaciones, etc.)
            $table->text('justification')->nullable();
            
            // Metadatos adicionales (IP, user agent, etc.)
            $table->json('metadata')->nullable();
            
            // Índices para consultas eficientes
            $table->index(['auditable_type', 'auditable_id']);
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
