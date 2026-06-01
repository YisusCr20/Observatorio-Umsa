<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('turno_id')->constrained('turnos');
    $table->foreignId('horario_id')->constrained('horarios'); // <--- DEJA SOLO ESTA

    $table->string('nombre');
    $table->string('correo');
    $table->string('telefono');
    $table->date('fecha');
    $table->integer('cantidad_personas')->default(1);
    $table->text('descripcion')->nullable();
    $table->string('estado')->default('Pendiente');
    $table->boolean('notificacion_leida')->default(false);
    $table->timestamps();
});
    }
};