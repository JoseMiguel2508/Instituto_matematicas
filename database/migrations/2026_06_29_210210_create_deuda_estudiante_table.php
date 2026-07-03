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
        // 1. Create DEUDA_ESTUDIANTE
        Schema::create('DEUDA_ESTUDIANTE', function (Blueprint $table) {
            $table->increments('id_deuda');
            $table->integer('id_estudiante')->unsigned();
            $table->integer('id_periodo')->unsigned();
            $table->integer('id_concepto')->unsigned();
            $table->decimal('monto', 8, 2);
            $table->string('estado', 20)->default('Pendiente'); // Pendiente, Pagado, Anulado
            $table->dateTime('fecha_generacion')->useCurrent();
            
            $table->foreign('id_estudiante')->references('id_estudiante')->on('ESTUDIANTE')->onDelete('cascade');
            $table->foreign('id_periodo')->references('id_periodo')->on('PERIODO_ACADEMICO')->onDelete('cascade');
            $table->foreign('id_concepto')->references('id_concepto')->on('CONCEPTO_PAGO')->onDelete('cascade');
        });

        // 2. Add id_deuda to DETALLE_PAGO
        Schema::table('DETALLE_PAGO', function (Blueprint $table) {
            $table->integer('id_deuda')->unsigned()->nullable()->after('id_concepto');
            $table->foreign('id_deuda')->references('id_deuda')->on('DEUDA_ESTUDIANTE')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('DETALLE_PAGO', function (Blueprint $table) {
            $table->dropForeign(['id_deuda']);
            $table->dropColumn('id_deuda');
        });
        
        Schema::dropIfExists('DEUDA_ESTUDIANTE');
    }
};
