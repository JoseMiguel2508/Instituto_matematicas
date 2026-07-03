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
        Schema::create('CAJA', function (Blueprint $table) {
            $table->id('id_caja');
            $table->string('nombre', 100);
            $table->enum('estado', ['Activa', 'Inactiva'])->default('Activa');
            $table->timestamps();
        });

        Schema::create('SESION_CAJA', function (Blueprint $table) {
            $table->id('id_sesion_caja');
            $table->unsignedBigInteger('id_caja');
            $table->unsignedInteger('id_usuario_apertura');
            $table->unsignedInteger('id_usuario_cierre')->nullable();
            
            $table->decimal('monto_inicial', 10, 2);
            $table->decimal('monto_final_esperado', 10, 2)->nullable();
            $table->decimal('monto_final_real', 10, 2)->nullable();
            $table->decimal('diferencia', 10, 2)->nullable();
            
            $table->dateTime('fecha_apertura');
            $table->dateTime('fecha_cierre')->nullable();
            
            $table->text('observaciones_apertura')->nullable();
            $table->text('observaciones_cierre')->nullable();
            
            $table->enum('estado', ['Abierta', 'Cerrada'])->default('Abierta');
            $table->timestamps();

            $table->foreign('id_caja')->references('id_caja')->on('CAJA');
            $table->foreign('id_usuario_apertura')->references('id_usuario')->on('USUARIO');
            $table->foreign('id_usuario_cierre')->references('id_usuario')->on('USUARIO');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('SESION_CAJA');
        Schema::dropIfExists('CAJA');
    }
};
