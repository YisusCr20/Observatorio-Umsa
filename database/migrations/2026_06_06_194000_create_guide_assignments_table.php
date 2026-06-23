<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guide_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('cargo')->nullable();
            $table->string('ci')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->date('fecha');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guide_assignments');
    }
};
