<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pagos')) {
            return;
        }

        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->constrained('reservas')->cascadeOnDelete();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('monto', 10, 2)->default(0);
            $table->string('metodo_pago', 40);
            $table->string('nro_comprobante', 120)->nullable();
            $table->string('estado_pago', 40)->default('Completado');
            $table->text('observacion')->nullable();
            $table->timestamp('pagado_en')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
