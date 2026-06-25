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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prestamo_id')->nullable()->constrained('prestamos')->nullOnDelete();
            $table->decimal('monto', 8, 2)->default(0);
            $table->date('fecha_pago')->nullable();
            $table->date('fecha_habilitacion')->nullable();
            $table->string('estado')->default('pendiente');
            $table->timestamp('pagado_en')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
