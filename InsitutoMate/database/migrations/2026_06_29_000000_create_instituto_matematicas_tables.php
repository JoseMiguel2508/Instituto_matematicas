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
        // 5. CARGO
        Schema::create('CARGO', function (Blueprint $table) {
            $table->increments('id_cargo');
            $table->string('nombre', 80)->unique();
            $table->string('descripcion', 200)->nullable();
            $table->integer('nivel_jerarquico')->nullable();
        });

        // 1. PERSONA
        Schema::create('PERSONA', function (Blueprint $table) {
            $table->increments('id_persona');
            $table->string('tipo_documento', 20);
            $table->string('numero_documento', 20)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->date('fecha_nacimiento')->nullable();
            $table->string('direccion', 200)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 100)->nullable();
        });

        // 2. ESTUDIANTE
        Schema::create('ESTUDIANTE', function (Blueprint $table) {
            $table->integer('id_estudiante')->unsigned()->primary();
            $table->string('codigo_estudiante', 20)->unique();
            $table->date('fecha_ingreso')->nullable();
            $table->string('estado', 20)->default('Activo');

            $table->foreign('id_estudiante')->references('id_persona')->on('PERSONA')->onDelete('cascade');
        });

        // 3. DOCENTE
        Schema::create('DOCENTE', function (Blueprint $table) {
            $table->integer('id_docente')->unsigned()->primary();
            $table->string('codigo_docente', 20)->unique();
            $table->string('grado_academico', 50)->nullable();
            $table->date('fecha_contratacion')->nullable();
            $table->string('estado', 20)->default('Activo');

            $table->foreign('id_docente')->references('id_persona')->on('PERSONA')->onDelete('cascade');
        });

        // 4. EMPLEADO
        Schema::create('EMPLEADO', function (Blueprint $table) {
            $table->integer('id_empleado')->unsigned()->primary();
            $table->string('codigo_empleado', 20)->unique();
            $table->integer('id_cargo')->unsigned();
            $table->date('fecha_contratacion')->nullable();
            $table->string('tipo_contrato', 30)->nullable();
            $table->string('estado', 20)->default('Activo');

            $table->foreign('id_empleado')->references('id_persona')->on('PERSONA')->onDelete('cascade');
            $table->foreign('id_cargo')->references('id_cargo')->on('CARGO');
        });

        // 6. ESPECIALIZACION_DOCENTE
        Schema::create('ESPECIALIZACION_DOCENTE', function (Blueprint $table) {
            $table->increments('id_especializacion_docente');
            $table->string('nombre', 100)->unique();
            $table->string('descripcion', 200)->nullable();
            $table->string('estado', 20)->default('Activo');
        });

        // 7. DOCENTE_ESPECIALIZACION
        Schema::create('DOCENTE_ESPECIALIZACION', function (Blueprint $table) {
            $table->increments('id_docente_especializacion');
            $table->integer('id_docente')->unsigned();
            $table->integer('id_especializacion_docente')->unsigned();
            $table->string('nivel_experiencia', 30)->nullable();
            $table->boolean('es_principal')->default(false);

            $table->unique(['id_docente', 'id_especializacion_docente'], 'uq_doc_esp');
            $table->foreign('id_docente')->references('id_docente')->on('DOCENTE')->onDelete('cascade');
            $table->foreign('id_especializacion_docente')->references('id_especializacion_docente')->on('ESPECIALIZACION_DOCENTE')->onDelete('cascade');
        });

        // 8. USUARIO
        Schema::create('USUARIO', function (Blueprint $table) {
            $table->increments('id_usuario');
            $table->integer('id_persona')->unsigned()->nullable();
            $table->string('username', 50)->unique();
            $table->string('password_hash', 255);
            $table->string('estado', 20)->default('Activo');
            $table->dateTime('fecha_creacion')->useCurrent();
            $table->dateTime('ultimo_acceso')->nullable();
            $table->integer('intentos_fallidos')->default(0);

            $table->foreign('id_persona')->references('id_persona')->on('PERSONA')->onDelete('set null');
        });

        // 9. ROL
        Schema::create('ROL', function (Blueprint $table) {
            $table->increments('id_rol');
            $table->string('nombre', 50)->unique();
            $table->string('descripcion', 200)->nullable();
        });

        // 10. USUARIO_ROL
        Schema::create('USUARIO_ROL', function (Blueprint $table) {
            $table->increments('id_usuario_rol');
            $table->integer('id_usuario')->unsigned();
            $table->integer('id_rol')->unsigned();
            $table->dateTime('fecha_asignacion')->useCurrent();

            $table->unique(['id_usuario', 'id_rol']);
            $table->foreign('id_usuario')->references('id_usuario')->on('USUARIO')->onDelete('cascade');
            $table->foreign('id_rol')->references('id_rol')->on('ROL')->onDelete('cascade');
        });

        // 11. PERIODO_ACADEMICO
        Schema::create('PERIODO_ACADEMICO', function (Blueprint $table) {
            $table->increments('id_periodo');
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('tipo', 20);
            $table->string('estado', 20)->default('Planificado');
        });

        // 12. ESPECIALIDAD
        Schema::create('ESPECIALIDAD', function (Blueprint $table) {
            $table->increments('id_especialidad');
            $table->string('codigo', 10)->unique();
            $table->string('nombre', 100)->unique();
            $table->string('descripcion', 300)->nullable();
            $table->string('estado', 20)->default('Activa');
        });

        // 13. NIVEL
        Schema::create('NIVEL', function (Blueprint $table) {
            $table->increments('id_nivel');
            $table->string('nombre', 50)->unique();
            $table->integer('orden')->unique();
            $table->string('descripcion', 200)->nullable();
        });

        // 14. CURSO
        Schema::create('CURSO', function (Blueprint $table) {
            $table->increments('id_curso');
            $table->integer('id_especialidad')->unsigned();
            $table->integer('id_nivel')->unsigned();
            $table->string('codigo_curso', 20)->unique();
            $table->string('nombre_curso', 150)->unique();
            $table->integer('creditos')->nullable();
            $table->integer('duracion_horas')->nullable();
            $table->string('estado', 20)->default('Activo');

            $table->foreign('id_especialidad')->references('id_especialidad')->on('ESPECIALIDAD');
            $table->foreign('id_nivel')->references('id_nivel')->on('NIVEL');
        });

        // 15. CURSO_PRERREQUISITO
        Schema::create('CURSO_PRERREQUISITO', function (Blueprint $table) {
            $table->increments('id_prerequisito');
            $table->integer('id_curso')->unsigned();
            $table->integer('id_curso_prerequisito')->unsigned();
            $table->decimal('nota_minima', 4, 2)->default(11.00);
            $table->string('tipo', 20)->default('Obligatorio');
            $table->string('condicion', 20)->default('Aprobado');

            $table->unique(['id_curso', 'id_curso_prerequisito'], 'uq_curso_prereq');
            $table->foreign('id_curso')->references('id_curso')->on('CURSO')->onDelete('cascade');
            $table->foreign('id_curso_prerequisito')->references('id_curso')->on('CURSO')->onDelete('cascade');
        });

        // 16. AULA
        Schema::create('AULA', function (Blueprint $table) {
            $table->increments('id_aula');
            $table->string('codigo_aula', 20)->unique();
            $table->integer('capacidad')->nullable();
            $table->string('ubicacion', 100)->nullable();
            $table->string('tipo', 30)->nullable();
            $table->string('estado', 20)->default('Disponible');
        });

        // 17. GRUPO
        Schema::create('GRUPO', function (Blueprint $table) {
            $table->increments('id_grupo');
            $table->integer('id_curso')->unsigned();
            $table->integer('id_docente')->unsigned();
            $table->integer('id_aula')->unsigned();
            $table->integer('id_periodo')->unsigned();
            $table->integer('numero_grupo');
            $table->integer('cupo_maximo')->nullable();
            $table->string('estado', 20)->default('Abierto');

            $table->unique(['id_curso', 'id_periodo', 'numero_grupo'], 'uq_grupo_cur_per_num');
            $table->foreign('id_curso')->references('id_curso')->on('CURSO');
            $table->foreign('id_docente')->references('id_docente')->on('DOCENTE');
            $table->foreign('id_aula')->references('id_aula')->on('AULA');
            $table->foreign('id_periodo')->references('id_periodo')->on('PERIODO_ACADEMICO');
        });

        // 18. HORARIO
        Schema::create('HORARIO', function (Blueprint $table) {
            $table->increments('id_horario');
            $table->integer('id_grupo')->unsigned();
            $table->string('dia_semana', 15);
            $table->time('hora_inicio');
            $table->time('hora_fin');

            $table->foreign('id_grupo')->references('id_grupo')->on('GRUPO')->onDelete('cascade');
        });

        // 19. MATRICULA
        Schema::create('MATRICULA', function (Blueprint $table) {
            $table->increments('id_matricula');
            $table->integer('id_estudiante')->unsigned();
            $table->integer('id_periodo')->unsigned();
            $table->integer('id_especialidad')->unsigned();
            $table->dateTime('fecha_matricula')->useCurrent();
            $table->string('tipo', 25)->default('Regular');
            $table->string('estado', 20)->default('Activa');
            $table->string('observaciones', 300)->nullable();
            $table->integer('id_usuario_registra')->unsigned();

            $table->unique(['id_estudiante', 'id_periodo'], 'uq_matricula_est_per');
            $table->foreign('id_estudiante')->references('id_estudiante')->on('ESTUDIANTE');
            $table->foreign('id_periodo')->references('id_periodo')->on('PERIODO_ACADEMICO');
            $table->foreign('id_especialidad')->references('id_especialidad')->on('ESPECIALIDAD');
            $table->foreign('id_usuario_registra')->references('id_usuario')->on('USUARIO');
        });

        // 20. INSCRIPCION
        Schema::create('INSCRIPCION', function (Blueprint $table) {
            $table->increments('id_inscripcion');
            $table->integer('id_matricula')->unsigned();
            $table->dateTime('fecha_inscripcion')->useCurrent();
            $table->string('estado', 20)->default('Activa');
            $table->integer('id_usuario_registra')->unsigned();

            $table->foreign('id_matricula')->references('id_matricula')->on('MATRICULA')->onDelete('cascade');
            $table->foreign('id_usuario_registra')->references('id_usuario')->on('USUARIO');
        });

        // 21. DETALLE_INSCRIPCION
        Schema::create('DETALLE_INSCRIPCION', function (Blueprint $table) {
            $table->increments('id_detalle_inscripcion');
            $table->integer('id_inscripcion')->unsigned();
            $table->integer('id_grupo')->unsigned();
            $table->string('estado', 20)->default('Inscrito');

            $table->unique(['id_inscripcion', 'id_grupo'], 'uq_det_inscripcion_ins_grp');
            $table->foreign('id_inscripcion')->references('id_inscripcion')->on('INSCRIPCION')->onDelete('cascade');
            $table->foreign('id_grupo')->references('id_grupo')->on('GRUPO');
        });

        // 22. NOTA_FINAL
        Schema::create('NOTA_FINAL', function (Blueprint $table) {
            $table->increments('id_nota_final');
            $table->integer('id_detalle_inscripcion')->unsigned()->unique();
            $table->decimal('nota', 4, 2);
            $table->string('estado', 20);
            $table->integer('id_usuario_registra')->unsigned();
            $table->dateTime('fecha_registro')->useCurrent();
            $table->string('observaciones', 200)->nullable();

            $table->foreign('id_detalle_inscripcion')->references('id_detalle_inscripcion')->on('DETALLE_INSCRIPCION')->onDelete('cascade');
            $table->foreign('id_usuario_registra')->references('id_usuario')->on('USUARIO');
        });

        // 23. CONCEPTO_PAGO
        Schema::create('CONCEPTO_PAGO', function (Blueprint $table) {
            $table->increments('id_concepto');
            $table->string('codigo', 10)->unique();
            $table->string('nombre', 100)->unique();
            $table->decimal('monto_base', 8, 2)->nullable();
            $table->string('tipo', 20);
            $table->boolean('es_obligatorio')->default(false);
            $table->string('estado', 20)->default('Activo');
        });

        // 24. PAGO
        Schema::create('PAGO', function (Blueprint $table) {
            $table->increments('id_pago');
            $table->integer('id_estudiante')->unsigned();
            $table->integer('id_matricula')->unsigned()->nullable();
            $table->integer('id_usuario_registra')->unsigned();
            $table->string('numero_comprobante', 30)->nullable();
            $table->string('tipo_comprobante', 20)->nullable();
            $table->decimal('monto_total', 8, 2);
            $table->dateTime('fecha_pago')->useCurrent();
            $table->string('metodo_pago', 30)->nullable();
            $table->string('estado', 20)->default('Registrado');
            $table->string('observaciones', 300)->nullable();

            $table->foreign('id_estudiante')->references('id_estudiante')->on('ESTUDIANTE');
            $table->foreign('id_matricula')->references('id_matricula')->on('MATRICULA')->onDelete('set null');
            $table->foreign('id_usuario_registra')->references('id_usuario')->on('USUARIO');
        });

        // 25. DETALLE_PAGO
        Schema::create('DETALLE_PAGO', function (Blueprint $table) {
            $table->increments('id_detalle_pago');
            $table->integer('id_pago')->unsigned();
            $table->integer('id_concepto')->unsigned();
            $table->decimal('monto_aplicado', 8, 2);
            $table->string('descripcion', 200)->nullable();

            $table->foreign('id_pago')->references('id_pago')->on('PAGO')->onDelete('cascade');
            $table->foreign('id_concepto')->references('id_concepto')->on('CONCEPTO_PAGO');
        });

        // 26. LOG_ACTIVIDAD
        Schema::create('LOG_ACTIVIDAD', function (Blueprint $table) {
            $table->increments('id_log');
            $table->integer('id_usuario')->unsigned();
            $table->string('accion', 20);
            $table->string('tabla_afectada', 50);
            $table->integer('id_registro_afectado')->nullable();
            $table->longText('datos_anteriores')->nullable();
            $table->longText('datos_nuevos')->nullable();
            $table->dateTime('fecha_hora')->useCurrent();
            $table->string('direccion_ip', 45)->nullable();
            $table->string('modulo', 30);

            $table->foreign('id_usuario')->references('id_usuario')->on('USUARIO')->onDelete('cascade');
        });

        // Additional indexes as defined in the SQL script
        Schema::table('PERSONA', function (Blueprint $table) {
            $table->index(['apellidos', 'nombres'], 'IX_PERSONA_apellidos');
        });
        Schema::table('ESTUDIANTE', function (Blueprint $table) {
            $table->index('estado', 'IX_ESTUDIANTE_estado');
        });
        Schema::table('DOCENTE', function (Blueprint $table) {
            $table->index('estado', 'IX_DOCENTE_estado');
        });
        Schema::table('EMPLEADO', function (Blueprint $table) {
            $table->index('estado', 'IX_EMPLEADO_estado');
        });
        Schema::table('USUARIO', function (Blueprint $table) {
            $table->index('estado', 'IX_USUARIO_estado');
        });
        Schema::table('CURSO', function (Blueprint $table) {
            $table->index('estado', 'IX_CURSO_estado');
        });
        Schema::table('GRUPO', function (Blueprint $table) {
            $table->index('id_periodo', 'IX_GRUPO_periodo');
            $table->index('id_docente', 'IX_GRUPO_docente');
            $table->index('id_curso', 'IX_GRUPO_curso');
        });
        Schema::table('HORARIO', function (Blueprint $table) {
            $table->index('id_grupo', 'IX_HORARIO_grupo');
        });
        Schema::table('MATRICULA', function (Blueprint $table) {
            $table->index('id_periodo', 'IX_MATRICULA_periodo');
            $table->index('id_estudiante', 'IX_MATRICULA_estudiante');
        });
        Schema::table('INSCRIPCION', function (Blueprint $table) {
            $table->index('id_matricula', 'IX_INSCRIPCION_matricula');
        });
        Schema::table('DETALLE_INSCRIPCION', function (Blueprint $table) {
            $table->index('id_grupo', 'IX_DETALLE_INSCRIPCION_grupo');
            $table->index('id_inscripcion', 'IX_DETALLE_INSCRIPCION_inscripcion');
        });
        Schema::table('NOTA_FINAL', function (Blueprint $table) {
            $table->index('id_detalle_inscripcion', 'IX_NOTA_FINAL_detalle');
        });
        Schema::table('PAGO', function (Blueprint $table) {
            $table->index('id_estudiante', 'IX_PAGO_estudiante');
            $table->index('fecha_pago', 'IX_PAGO_fecha');
        });
        Schema::table('DETALLE_PAGO', function (Blueprint $table) {
            $table->index('id_pago', 'IX_DETALLE_PAGO_pago');
            $table->index('id_concepto', 'IX_DETALLE_PAGO_concepto');
        });
        Schema::table('LOG_ACTIVIDAD', function (Blueprint $table) {
            $table->index('id_usuario', 'IX_LOG_ACTIVIDAD_usuario');
            $table->index('fecha_hora', 'IX_LOG_ACTIVIDAD_fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dropping tables in reverse order of creation to respect constraints
        Schema::dropIfExists('LOG_ACTIVIDAD');
        Schema::dropIfExists('DETALLE_PAGO');
        Schema::dropIfExists('PAGO');
        Schema::dropIfExists('CONCEPTO_PAGO');
        Schema::dropIfExists('NOTA_FINAL');
        Schema::dropIfExists('DETALLE_INSCRIPCION');
        Schema::dropIfExists('INSCRIPCION');
        Schema::dropIfExists('MATRICULA');
        Schema::dropIfExists('HORARIO');
        Schema::dropIfExists('GRUPO');
        Schema::dropIfExists('AULA');
        Schema::dropIfExists('CURSO_PRERREQUISITO');
        Schema::dropIfExists('CURSO');
        Schema::dropIfExists('NIVEL');
        Schema::dropIfExists('ESPECIALIDAD');
        Schema::dropIfExists('PERIODO_ACADEMICO');
        Schema::dropIfExists('USUARIO_ROL');
        Schema::dropIfExists('ROL');
        Schema::dropIfExists('USUARIO');
        Schema::dropIfExists('DOCENTE_ESPECIALIZACION');
        Schema::dropIfExists('ESPECIALIZACION_DOCENTE');
        Schema::dropIfExists('EMPLEADO');
        Schema::dropIfExists('DOCENTE');
        Schema::dropIfExists('ESTUDIANTE');
        Schema::dropIfExists('PERSONA');
        Schema::dropIfExists('CARGO');
    }
};
