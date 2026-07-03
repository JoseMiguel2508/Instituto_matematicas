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
        // Triggers para ESTUDIANTE
        DB::unprepared("
            CREATE TRIGGER trg_estudiante_after_insert
            AFTER INSERT ON estudiante
            FOR EACH ROW
            BEGIN
                DECLARE v_usuario_id INT;
                DECLARE v_ip VARCHAR(45);
                SET v_usuario_id = IFNULL(@usuario_id, 1);
                SET v_ip = IFNULL(@user_ip, '127.0.0.1');
                
                INSERT INTO log_actividad (id_usuario, accion, tabla_afectada, id_registro_afectado, datos_nuevos, direccion_ip, modulo)
                VALUES (v_usuario_id, 'INSERT', 'ESTUDIANTE', NEW.id_estudiante, JSON_OBJECT('id_estudiante', NEW.id_estudiante, 'codigo_estudiante', NEW.codigo_estudiante, 'fecha_ingreso', NEW.fecha_ingreso, 'estado', NEW.estado), v_ip, 'Estudiantes');
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_estudiante_after_update
            AFTER UPDATE ON estudiante
            FOR EACH ROW
            BEGIN
                DECLARE v_usuario_id INT;
                DECLARE v_ip VARCHAR(45);
                SET v_usuario_id = IFNULL(@usuario_id, 1);
                SET v_ip = IFNULL(@user_ip, '127.0.0.1');
                
                INSERT INTO log_actividad (id_usuario, accion, tabla_afectada, id_registro_afectado, datos_anteriores, datos_nuevos, direccion_ip, modulo)
                VALUES (v_usuario_id, 'UPDATE', 'ESTUDIANTE', NEW.id_estudiante, 
                        JSON_OBJECT('id_estudiante', OLD.id_estudiante, 'codigo_estudiante', OLD.codigo_estudiante, 'fecha_ingreso', OLD.fecha_ingreso, 'estado', OLD.estado),
                        JSON_OBJECT('id_estudiante', NEW.id_estudiante, 'codigo_estudiante', NEW.codigo_estudiante, 'fecha_ingreso', NEW.fecha_ingreso, 'estado', NEW.estado),
                        v_ip, 'Estudiantes');
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_estudiante_after_delete
            AFTER DELETE ON estudiante
            FOR EACH ROW
            BEGIN
                DECLARE v_usuario_id INT;
                DECLARE v_ip VARCHAR(45);
                SET v_usuario_id = IFNULL(@usuario_id, 1);
                SET v_ip = IFNULL(@user_ip, '127.0.0.1');
                
                INSERT INTO log_actividad (id_usuario, accion, tabla_afectada, id_registro_afectado, datos_anteriores, direccion_ip, modulo)
                VALUES (v_usuario_id, 'DELETE', 'ESTUDIANTE', OLD.id_estudiante, 
                        JSON_OBJECT('id_estudiante', OLD.id_estudiante, 'codigo_estudiante', OLD.codigo_estudiante, 'fecha_ingreso', OLD.fecha_ingreso, 'estado', OLD.estado),
                        v_ip, 'Estudiantes');
            END
        ");
        
        // Triggers para PERSONA
        DB::unprepared("
            CREATE TRIGGER trg_persona_after_insert
            AFTER INSERT ON persona
            FOR EACH ROW
            BEGIN
                DECLARE v_usuario_id INT;
                DECLARE v_ip VARCHAR(45);
                SET v_usuario_id = IFNULL(@usuario_id, 1);
                SET v_ip = IFNULL(@user_ip, '127.0.0.1');
                
                INSERT INTO log_actividad (id_usuario, accion, tabla_afectada, id_registro_afectado, datos_nuevos, direccion_ip, modulo)
                VALUES (v_usuario_id, 'INSERT', 'PERSONA', NEW.id_persona, JSON_OBJECT('id_persona', NEW.id_persona, 'nombres', NEW.nombres, 'apellidos', NEW.apellidos, 'numero_documento', NEW.numero_documento), v_ip, 'Estudiantes');
            END
        ");
        
        DB::unprepared("
            CREATE TRIGGER trg_persona_after_update
            AFTER UPDATE ON persona
            FOR EACH ROW
            BEGIN
                DECLARE v_usuario_id INT;
                DECLARE v_ip VARCHAR(45);
                SET v_usuario_id = IFNULL(@usuario_id, 1);
                SET v_ip = IFNULL(@user_ip, '127.0.0.1');
                
                INSERT INTO log_actividad (id_usuario, accion, tabla_afectada, id_registro_afectado, datos_anteriores, datos_nuevos, direccion_ip, modulo)
                VALUES (v_usuario_id, 'UPDATE', 'PERSONA', NEW.id_persona, 
                        JSON_OBJECT('id_persona', OLD.id_persona, 'nombres', OLD.nombres, 'apellidos', OLD.apellidos, 'numero_documento', OLD.numero_documento),
                        JSON_OBJECT('id_persona', NEW.id_persona, 'nombres', NEW.nombres, 'apellidos', NEW.apellidos, 'numero_documento', NEW.numero_documento),
                        v_ip, 'Estudiantes');
            END
        ");
        
        DB::unprepared("
            CREATE TRIGGER trg_persona_after_delete
            AFTER DELETE ON persona
            FOR EACH ROW
            BEGIN
                DECLARE v_usuario_id INT;
                DECLARE v_ip VARCHAR(45);
                SET v_usuario_id = IFNULL(@usuario_id, 1);
                SET v_ip = IFNULL(@user_ip, '127.0.0.1');
                
                INSERT INTO log_actividad (id_usuario, accion, tabla_afectada, id_registro_afectado, datos_anteriores, direccion_ip, modulo)
                VALUES (v_usuario_id, 'DELETE', 'PERSONA', OLD.id_persona, 
                        JSON_OBJECT('id_persona', OLD.id_persona, 'nombres', OLD.nombres, 'apellidos', OLD.apellidos, 'numero_documento', OLD.numero_documento),
                        v_ip, 'Estudiantes');
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_estudiante_after_insert");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_estudiante_after_update");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_estudiante_after_delete");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_persona_after_insert");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_persona_after_update");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_persona_after_delete");
    }
};
