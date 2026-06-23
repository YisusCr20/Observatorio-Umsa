<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitados_reserva', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('cargo')->nullable();
            $table->string('institucion')->nullable();
            $table->string('pais')->nullable();
            $table->string('correo')->nullable();
            $table->string('telefono')->nullable();
            $table->string('tipo_visita')->default('Invitado especial');
            $table->date('fecha');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->unsignedInteger('cantidad_personas')->default(1);
            $table->text('motivo')->nullable();
            $table->text('observacion')->nullable();
            $table->string('estado')->default('Confirmado');
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitados_reserva');
    }
};
