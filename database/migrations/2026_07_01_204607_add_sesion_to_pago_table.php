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
        Schema::table('PAGO', function (Blueprint $table) {
            $table->unsignedBigInteger('id_sesion_caja')->nullable()->after('id_pago');
            $table->foreign('id_sesion_caja')->references('id_sesion_caja')->on('SESION_CAJA');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('PAGO', function (Blueprint $table) {
            $table->dropForeign(['id_sesion_caja']);
            $table->dropColumn('id_sesion_caja');
        });
    }
};
