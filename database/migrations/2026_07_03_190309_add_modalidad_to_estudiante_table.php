<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('estudiante', function (Blueprint $table) {
            $table->string('modalidad_estudio', 20)->default('Especialidad')->after('fecha_ingreso');
        });

        // Modificamos sp_crear_estudiante
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_crear_estudiante");
        DB::unprepared("
            CREATE PROCEDURE sp_crear_estudiante(
                IN p_tipo_documento VARCHAR(20),
                IN p_numero_documento VARCHAR(20),
                IN p_nombres VARCHAR(100),
                IN p_apellidos VARCHAR(100),
                IN p_fecha_nacimiento DATE,
                IN p_direccion VARCHAR(200),
                IN p_telefono VARCHAR(20),
                IN p_email VARCHAR(100),
                IN p_codigo_estudiante VARCHAR(20),
                IN p_modalidad_estudio VARCHAR(20)
            )
            BEGIN
                DECLARE v_id_persona INT;
                DECLARE v_id_periodo INT;
                DECLARE v_id_concepto_mat INT;
                DECLARE v_id_concepto_pen INT;
                DECLARE v_monto_mat DECIMAL(8,2);
                DECLARE v_monto_pen DECIMAL(8,2);
                
                -- Manejo de transacciones
                DECLARE exit handler for sqlexception
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;
                
                START TRANSACTION;
                
                -- Insertar Persona
                INSERT INTO persona (tipo_documento, numero_documento, nombres, apellidos, fecha_nacimiento, direccion, telefono, email)
                VALUES (p_tipo_documento, p_numero_documento, p_nombres, p_apellidos, p_fecha_nacimiento, p_direccion, p_telefono, p_email);
                
                SET v_id_persona = LAST_INSERT_ID();
                
                -- Insertar Estudiante
                INSERT INTO estudiante (id_estudiante, codigo_estudiante, fecha_ingreso, modalidad_estudio, estado)
                VALUES (v_id_persona, p_codigo_estudiante, CURRENT_DATE(), p_modalidad_estudio, 'Activo');
                
                -- Generar Deudas Iniciales SOLO si la modalidad es 'Especialidad'
                IF p_modalidad_estudio = 'Especialidad' THEN
                    SELECT id_periodo INTO v_id_periodo FROM periodo_academico WHERE estado = 'En Curso' LIMIT 1;
                    
                    IF v_id_periodo IS NOT NULL THEN
                        SELECT id_concepto, monto_base INTO v_id_concepto_mat, v_monto_mat FROM concepto_pago WHERE nombre LIKE 'Matrícula%' LIMIT 1;
                        IF v_id_concepto_mat IS NOT NULL THEN
                            INSERT INTO deuda_estudiante (id_estudiante, id_periodo, id_concepto, monto, estado, fecha_generacion)
                            VALUES (v_id_persona, v_id_periodo, v_id_concepto_mat, IFNULL(v_monto_mat, 150.00), 'Pendiente', NOW());
                        END IF;
                        
                        SELECT id_concepto, monto_base INTO v_id_concepto_pen, v_monto_pen FROM concepto_pago WHERE nombre LIKE 'Pensión%' LIMIT 1;
                        IF v_id_concepto_pen IS NOT NULL THEN
                            INSERT INTO deuda_estudiante (id_estudiante, id_periodo, id_concepto, monto, estado, fecha_generacion)
                            VALUES (v_id_persona, v_id_periodo, v_id_concepto_pen, IFNULL(v_monto_pen, 450.00), 'Pendiente', NOW());
                        END IF;
                    END IF;
                END IF;
                
                COMMIT;
            END
        ");

        // Modificamos sp_actualizar_estudiante
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_actualizar_estudiante");
        DB::unprepared("
            CREATE PROCEDURE sp_actualizar_estudiante(
                IN p_id_estudiante INT,
                IN p_tipo_documento VARCHAR(20),
                IN p_numero_documento VARCHAR(20),
                IN p_nombres VARCHAR(100),
                IN p_apellidos VARCHAR(100),
                IN p_fecha_nacimiento DATE,
                IN p_direccion VARCHAR(200),
                IN p_telefono VARCHAR(20),
                IN p_email VARCHAR(100),
                IN p_estado VARCHAR(20),
                IN p_modalidad_estudio VARCHAR(20)
            )
            BEGIN
                DECLARE exit handler for sqlexception
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;
                
                START TRANSACTION;
                
                IF p_nombres IS NOT NULL THEN
                    UPDATE persona SET 
                        tipo_documento = p_tipo_documento,
                        numero_documento = p_numero_documento,
                        nombres = p_nombres,
                        apellidos = p_apellidos,
                        fecha_nacimiento = p_fecha_nacimiento,
                        direccion = p_direccion,
                        telefono = p_telefono,
                        email = p_email
                    WHERE id_persona = p_id_estudiante;
                END IF;
                
                UPDATE estudiante SET estado = p_estado, modalidad_estudio = p_modalidad_estudio WHERE id_estudiante = p_id_estudiante;
                
                COMMIT;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estudiante', function (Blueprint $table) {
            $table->dropColumn('modalidad_estudio');
        });
        
        // Restore old procedures logically
        // ...
    }
};
