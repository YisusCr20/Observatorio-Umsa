<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            // Se conecta con turnos primero
            $table->foreignId('turno_id')->constrained('turnos')->onDelete('cascade');
            $table->time('hora_inicio'); // Dato obligatorio para el select dinámico
            $table->time('hora_fin')->nullable(); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('horarios');
    }
};