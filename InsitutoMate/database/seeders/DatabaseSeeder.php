<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Persona;
use App\Models\Docente;
use App\Models\Empleado;
use App\Models\Cargo;
use App\Models\Rol;
use App\Models\PeriodoAcademico;
use App\Models\Especialidad;
use App\Models\Nivel;
use App\Models\Curso;
use App\Models\Aula;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\ConceptoPago;
use App\Models\CursoPrerrequisito;
use App\Models\LogActividad;
use App\Models\Pago;
use App\Models\DetallePago;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // ══════════════════════════════════════════════════════════════════
        // 1. ROLES
        // ══════════════════════════════════════════════════════════════════
        $roles = [
            ['nombre' => 'Administrador', 'descripcion' => 'Administrador total del sistema'],
            ['nombre' => 'Director',      'descripcion' => 'Director del Instituto'],
            ['nombre' => 'Coordinador',   'descripcion' => 'Coordinador Académico'],
            ['nombre' => 'Docente',       'descripcion' => 'Docente del Instituto'],
            ['nombre' => 'Secretaria',    'descripcion' => 'Personal administrativo y de registro'],
            ['nombre' => 'Cajero',        'descripcion' => 'Encargado de Caja y Cobranzas'],
        ];
        foreach ($roles as $r) {
            Rol::firstOrCreate(['nombre' => $r['nombre']], $r);
        }

        // ══════════════════════════════════════════════════════════════════
        // 2. CARGOS
        // ══════════════════════════════════════════════════════════════════
        $cargos = [
            ['nombre' => 'Director General',      'descripcion' => 'Máxima autoridad',         'nivel_jerarquico' => 1],
            ['nombre' => 'Coordinador Académico', 'descripcion' => 'Coordinación de cursos',   'nivel_jerarquico' => 2],
            ['nombre' => 'Secretaria Académica',  'descripcion' => 'Atención y registros',     'nivel_jerarquico' => 3],
            ['nombre' => 'Cajero',                'descripcion' => 'Responsable de caja',      'nivel_jerarquico' => 3],
        ];
        foreach ($cargos as $c) {
            Cargo::firstOrCreate(['nombre' => $c['nombre']], $c);
        }

        // ══════════════════════════════════════════════════════════════════
        // 3. PERSONAS DEL PERSONAL (Admin + Staff)
        // ══════════════════════════════════════════════════════════════════
        $adminPersona = Persona::create([
            'tipo_documento' => 'DNI', 'numero_documento' => '10000000',
            'nombres' => 'Administrador', 'apellidos' => 'Sistema',
            'fecha_nacimiento' => '1990-01-01', 'direccion' => 'Av. Central 123',
            'telefono' => '999888777', 'email' => 'admin@instituto.edu.pe',
        ]);
        $secPersona = Persona::create([
            'tipo_documento' => 'DNI', 'numero_documento' => '10000001',
            'nombres' => 'María', 'apellidos' => 'Gonzáles',
            'fecha_nacimiento' => '1992-05-10', 'direccion' => 'Calle Los Claveles 456',
            'telefono' => '987654321', 'email' => 'maria@instituto.edu.pe',
        ]);
        $cajPersona = Persona::create([
            'tipo_documento' => 'DNI', 'numero_documento' => '10000002',
            'nombres' => 'Juan', 'apellidos' => 'Cajero',
            'fecha_nacimiento' => '1995-02-12', 'direccion' => 'Av. Banzer 456',
            'telefono' => '999111222', 'email' => 'cajero@instituto.edu.pe',
        ]);
        $coordPersona = Persona::create([
            'tipo_documento' => 'DNI', 'numero_documento' => '10000003',
            'nombres' => 'Patricia', 'apellidos' => 'Coordinadora',
            'fecha_nacimiento' => '1988-11-20', 'direccion' => 'Equipetrol Calle 8',
            'telefono' => '999333444', 'email' => 'coordinador@instituto.edu.pe',
        ]);

        // ══════════════════════════════════════════════════════════════════
        // 4. DOCENTES
        // ══════════════════════════════════════════════════════════════════
        $teacherData = [
            ['nombres' => 'Carlos',   'apellidos' => 'Huamán',  'email' => 'chuaman@instituto.edu.pe',  'grado' => 'Magister en Matemáticas',            'dni' => '20000010'],
            ['nombres' => 'Sofia',    'apellidos' => 'Ramos',   'email' => 'sramos@instituto.edu.pe',   'grado' => 'Doctor en Ciencias Matemáticas',     'dni' => '20000011'],
            ['nombres' => 'Luis',     'apellidos' => 'Mendoza', 'email' => 'lmendoza@instituto.edu.pe', 'grado' => 'Licenciado en Matemáticas',          'dni' => '20000012'],
            ['nombres' => 'Ana',      'apellidos' => 'Vargas',  'email' => 'avargas@instituto.edu.pe',  'grado' => 'Magister en Estadística Aplicada',   'dni' => '20000013'],
            ['nombres' => 'Roberto',  'apellidos' => 'Salinas', 'email' => 'rsalinas@instituto.edu.pe', 'grado' => 'Doctor en Matemática Computacional', 'dni' => '20000014'],
        ];

        $docentes = [];
        foreach ($teacherData as $t) {
            $p = Persona::create([
                'tipo_documento' => 'DNI', 'numero_documento' => $t['dni'],
                'nombres' => $t['nombres'], 'apellidos' => $t['apellidos'],
                'fecha_nacimiento' => '1980-08-15', 'direccion' => 'Urbanización San José',
                'telefono' => '955444' . substr($t['dni'], -3), 'email' => $t['email'],
            ]);
            $docentes[] = Docente::create([
                'id_docente'       => $p->id_persona,
                'codigo_docente'   => substr($t['dni'], -2),
                'grado_academico'  => $t['grado'],
                'fecha_contratacion' => '2020-03-01',
                'estado'           => 'Activo',
            ]);
        }

        // ══════════════════════════════════════════════════════════════════
        // 5. EMPLEADOS
        // ══════════════════════════════════════════════════════════════════
        $cargoSec   = Cargo::where('nombre', 'Secretaria Académica')->first();
        $cargoCaj   = Cargo::where('nombre', 'Cajero')->first();
        $cargoCoord = Cargo::where('nombre', 'Coordinador Académico')->first();

        Empleado::create(['id_empleado' => $secPersona->id_persona,   'codigo_empleado' => 'EMP-100', 'id_cargo' => $cargoSec->id_cargo,   'fecha_contratacion' => '2023-01-15', 'tipo_contrato' => 'Planilla', 'estado' => 'Activo']);
        Empleado::create(['id_empleado' => $cajPersona->id_persona,   'codigo_empleado' => 'EMP-200', 'id_cargo' => $cargoCaj->id_cargo,   'fecha_contratacion' => '2024-01-10', 'tipo_contrato' => 'CAS',     'estado' => 'Activo']);
        Empleado::create(['id_empleado' => $coordPersona->id_persona, 'codigo_empleado' => 'EMP-300', 'id_cargo' => $cargoCoord->id_cargo, 'fecha_contratacion' => '2022-06-01', 'tipo_contrato' => 'Planilla', 'estado' => 'Activo']);

        // ══════════════════════════════════════════════════════════════════
        // 6. USUARIOS Y ROLES
        // ══════════════════════════════════════════════════════════════════
        $adminUser = User::create(['id_persona' => $adminPersona->id_persona, 'username' => 'admin',        'password_hash' => Hash::make('admin123'),        'estado' => 'Activo']);
        $secUser   = User::create(['id_persona' => $secPersona->id_persona,   'username' => 'secretaria',   'password_hash' => Hash::make('secretaria123'),   'estado' => 'Activo']);
        $cajUser   = User::create(['id_persona' => $cajPersona->id_persona,   'username' => 'cajero',       'password_hash' => Hash::make('cajero123'),       'estado' => 'Activo']);
        $coordUser = User::create(['id_persona' => $coordPersona->id_persona, 'username' => 'coordinador',  'password_hash' => Hash::make('coordinador123'),  'estado' => 'Activo']);

        $docentesUsers = [];
        foreach ($docentes as $idx => $d) {
            $docentesUsers[] = User::create([
                'id_persona'    => $d->id_docente,
                'username'      => strtolower($teacherData[$idx]['nombres']),
                'password_hash' => Hash::make('docente123'),
                'estado'        => 'Activo',
            ]);
        }

        $rolAdmin      = Rol::where('nombre', 'Administrador')->first();
        $rolSecretaria = Rol::where('nombre', 'Secretaria')->first();
        $rolCajero     = Rol::where('nombre', 'Cajero')->first();
        $rolCoord      = Rol::where('nombre', 'Coordinador')->first();
        $rolDocente    = Rol::where('nombre', 'Docente')->first();

        $adminUser->roles()->attach($rolAdmin->id_rol);
        $secUser->roles()->attach($rolSecretaria->id_rol);
        $cajUser->roles()->attach($rolCajero->id_rol);
        $coordUser->roles()->attach($rolCoord->id_rol);
        foreach ($docentesUsers as $du) {
            $du->roles()->attach($rolDocente->id_rol);
        }

        // ══════════════════════════════════════════════════════════════════
        // 6.5. CAJA Y SESION DE CAJA (Para el cajero)
        // ══════════════════════════════════════════════════════════════════
        $cajaPrincipal = \App\Models\Caja::create([
            'nombre' => 'Caja Principal',
            'estado' => 'Activa'
        ]);

        $sesionAbierta = \App\Models\SesionCaja::create([
            'id_caja' => $cajaPrincipal->id_caja,
            'id_usuario_apertura' => $cajUser->id_usuario,
            'monto_inicial' => 200.00,
            'fecha_apertura' => Carbon::now()->startOfDay(),
            'estado' => 'Abierta'
        ]);

        // ══════════════════════════════════════════════════════════════════
        // 7. PERIODO ACADÉMICO
        // ══════════════════════════════════════════════════════════════════
        $periodo = PeriodoAcademico::create([
            'codigo' => '2026-I', 'nombre' => 'Periodo Académico 2026 – I',
            'fecha_inicio' => '2026-03-01', 'fecha_fin' => '2026-07-15',
            'tipo' => 'Regular', 'estado' => 'En Curso',
        ]);

        // ══════════════════════════════════════════════════════════════════
        // 8. ESPECIALIDADES
        // ══════════════════════════════════════════════════════════════════
        $espMat = Especialidad::create([
            'codigo' => 'LMA', 'nombre' => 'Licenciatura en Matemáticas Aplicadas',
            'descripcion' => 'Formación en modelamiento matemático y resolución de problemas reales.',
            'estado' => 'Activa',
        ]);
        $espEst = Especialidad::create([
            'codigo' => 'EST', 'nombre' => 'Técnico en Estadística y Análisis de Datos',
            'descripcion' => 'Carrera técnica enfocada en análisis estadístico y ciencia de datos.',
            'estado' => 'Activa',
        ]);

        // ══════════════════════════════════════════════════════════════════
        // 9. NIVELES
        // ══════════════════════════════════════════════════════════════════
        $n1 = Nivel::create(['nombre' => 'Primer Nivel',   'orden' => 1, 'descripcion' => 'Cursos base sin prerrequisitos']);
        $n2 = Nivel::create(['nombre' => 'Segundo Nivel',  'orden' => 2, 'descripcion' => 'Cursos intermedios']);
        $n3 = Nivel::create(['nombre' => 'Tercer Nivel',   'orden' => 3, 'descripcion' => 'Cursos avanzados']);

        // ══════════════════════════════════════════════════════════════════
        // 10. CATÁLOGO DE CURSOS
        //     (sin prerrequisitos = nivel 1; con prerrequisitos = niveles 2 y 3)
        // ══════════════════════════════════════════════════════════════════

        // ── NIVEL 1: Sin prerrequisitos ──
        $c101 = Curso::create(['id_especialidad' => $espMat->id_especialidad, 'id_nivel' => $n1->id_nivel, 'codigo_curso' => 'MAT-101', 'nombre_curso' => 'Cálculo I',              'creditos' => 5, 'duracion_horas' => 80,  'estado' => 'Activo']);
        $c102 = Curso::create(['id_especialidad' => $espMat->id_especialidad, 'id_nivel' => $n1->id_nivel, 'codigo_curso' => 'MAT-102', 'nombre_curso' => 'Álgebra Lineal I',       'creditos' => 4, 'duracion_horas' => 64,  'estado' => 'Activo']);
        $c103 = Curso::create(['id_especialidad' => $espMat->id_especialidad, 'id_nivel' => $n1->id_nivel, 'codigo_curso' => 'MAT-103', 'nombre_curso' => 'Geometría Analítica',    'creditos' => 4, 'duracion_horas' => 64,  'estado' => 'Activo']);
        $c104 = Curso::create(['id_especialidad' => $espMat->id_especialidad, 'id_nivel' => $n1->id_nivel, 'codigo_curso' => 'MAT-104', 'nombre_curso' => 'Lógica Matemática',      'creditos' => 3, 'duracion_horas' => 48,  'estado' => 'Activo']);
        $c105 = Curso::create(['id_especialidad' => $espEst->id_especialidad, 'id_nivel' => $n1->id_nivel, 'codigo_curso' => 'EST-101', 'nombre_curso' => 'Probabilidades I',        'creditos' => 4, 'duracion_horas' => 64,  'estado' => 'Activo']);
        $c106 = Curso::create(['id_especialidad' => $espEst->id_especialidad, 'id_nivel' => $n1->id_nivel, 'codigo_curso' => 'EST-102', 'nombre_curso' => 'Estadística Descriptiva', 'creditos' => 4, 'duracion_horas' => 64, 'estado' => 'Activo']);

        // ── NIVEL 2: Requieren prerrequisito de Nivel 1 ──
        $c201 = Curso::create(['id_especialidad' => $espMat->id_especialidad, 'id_nivel' => $n2->id_nivel, 'codigo_curso' => 'MAT-201', 'nombre_curso' => 'Cálculo II',             'creditos' => 5, 'duracion_horas' => 80,  'estado' => 'Activo']);
        $c202 = Curso::create(['id_especialidad' => $espMat->id_especialidad, 'id_nivel' => $n2->id_nivel, 'codigo_curso' => 'MAT-202', 'nombre_curso' => 'Álgebra Lineal II',      'creditos' => 4, 'duracion_horas' => 64,  'estado' => 'Activo']);
        $c203 = Curso::create(['id_especialidad' => $espMat->id_especialidad, 'id_nivel' => $n2->id_nivel, 'codigo_curso' => 'MAT-203', 'nombre_curso' => 'Ecuaciones Diferenciales','creditos'=> 5, 'duracion_horas' => 80,  'estado' => 'Activo']);
        $c204 = Curso::create(['id_especialidad' => $espMat->id_especialidad, 'id_nivel' => $n2->id_nivel, 'codigo_curso' => 'MAT-204', 'nombre_curso' => 'Geometría Diferencial',  'creditos' => 4, 'duracion_horas' => 64,  'estado' => 'Activo']);
        $c205 = Curso::create(['id_especialidad' => $espEst->id_especialidad, 'id_nivel' => $n2->id_nivel, 'codigo_curso' => 'EST-201', 'nombre_curso' => 'Probabilidades II',       'creditos' => 4, 'duracion_horas' => 64,  'estado' => 'Activo']);
        $c206 = Curso::create(['id_especialidad' => $espEst->id_especialidad, 'id_nivel' => $n2->id_nivel, 'codigo_curso' => 'EST-202', 'nombre_curso' => 'Estadística Inferencial', 'creditos' => 4, 'duracion_horas' => 64, 'estado' => 'Activo']);
        $c207 = Curso::create(['id_especialidad' => $espMat->id_especialidad, 'id_nivel' => $n2->id_nivel, 'codigo_curso' => 'MAT-205', 'nombre_curso' => 'Métodos Numéricos',       'creditos' => 4, 'duracion_horas' => 64,  'estado' => 'Activo']);

        // ── NIVEL 3: Requieren prerrequisitos de Nivel 2 ──
        $c301 = Curso::create(['id_especialidad' => $espMat->id_especialidad, 'id_nivel' => $n3->id_nivel, 'codigo_curso' => 'MAT-301', 'nombre_curso' => 'Cálculo III',             'creditos' => 5, 'duracion_horas' => 80,  'estado' => 'Activo']);
        $c302 = Curso::create(['id_especialidad' => $espMat->id_especialidad, 'id_nivel' => $n3->id_nivel, 'codigo_curso' => 'MAT-302', 'nombre_curso' => 'Análisis Real',           'creditos' => 5, 'duracion_horas' => 80,  'estado' => 'Activo']);
        $c303 = Curso::create(['id_especialidad' => $espMat->id_especialidad, 'id_nivel' => $n3->id_nivel, 'codigo_curso' => 'MAT-303', 'nombre_curso' => 'Álgebra Abstracta',       'creditos' => 4, 'duracion_horas' => 64,  'estado' => 'Activo']);
        $c304 = Curso::create(['id_especialidad' => $espEst->id_especialidad, 'id_nivel' => $n3->id_nivel, 'codigo_curso' => 'EST-301', 'nombre_curso' => 'Regresión y Series',      'creditos' => 4, 'duracion_horas' => 64,  'estado' => 'Activo']);
        $c305 = Curso::create(['id_especialidad' => $espEst->id_especialidad, 'id_nivel' => $n3->id_nivel, 'codigo_curso' => 'EST-302', 'nombre_curso' => 'Análisis Multivariado',   'creditos' => 4, 'duracion_horas' => 64,  'estado' => 'Activo']);
        $c306 = Curso::create(['id_especialidad' => $espMat->id_especialidad, 'id_nivel' => $n3->id_nivel, 'codigo_curso' => 'MAT-304', 'nombre_curso' => 'Investigación Operativa', 'creditos' => 4, 'duracion_horas' => 64, 'estado' => 'Activo']);

        // ══════════════════════════════════════════════════════════════════
        // 11. CADENA DE PRERREQUISITOS
        //     Regla: para inscribirse en un curso de nivel N,
        //     el estudiante DEBE HABER APROBADO el/los cursos listados aquí.
        // ══════════════════════════════════════════════════════════════════
        $prereqs = [
            // NIVEL 2 ← NIVEL 1
            [$c201->id_curso, $c101->id_curso], // Cálculo II ← Cálculo I
            [$c202->id_curso, $c102->id_curso], // Álgebra Lineal II ← Álgebra Lineal I
            [$c203->id_curso, $c101->id_curso], // Ec. Diferenciales ← Cálculo I
            [$c204->id_curso, $c103->id_curso], // Geometría Dif. ← Geometría Analítica
            [$c204->id_curso, $c101->id_curso], // Geometría Dif. ← Cálculo I (doble prereq)
            [$c205->id_curso, $c105->id_curso], // Probabilidades II ← Probabilidades I
            [$c206->id_curso, $c106->id_curso], // Est. Inferencial ← Est. Descriptiva
            [$c206->id_curso, $c105->id_curso], // Est. Inferencial ← Probabilidades I (doble prereq)
            [$c207->id_curso, $c101->id_curso], // Métodos Numéricos ← Cálculo I
            [$c207->id_curso, $c102->id_curso], // Métodos Numéricos ← Álgebra Lineal I

            // NIVEL 3 ← NIVEL 2
            [$c301->id_curso, $c201->id_curso], // Cálculo III ← Cálculo II
            [$c302->id_curso, $c201->id_curso], // Análisis Real ← Cálculo II
            [$c303->id_curso, $c202->id_curso], // Álgebra Abstracta ← Álgebra Lineal II
            [$c304->id_curso, $c206->id_curso], // Regresión ← Est. Inferencial
            [$c304->id_curso, $c205->id_curso], // Regresión ← Probabilidades II
            [$c305->id_curso, $c206->id_curso], // Análisis Multivariado ← Est. Inferencial
            [$c306->id_curso, $c207->id_curso], // Investigación Op. ← Métodos Numéricos
            [$c306->id_curso, $c202->id_curso], // Investigación Op. ← Álgebra Lineal II
        ];

        foreach ($prereqs as [$idCurso, $idPrereq]) {
            CursoPrerrequisito::firstOrCreate(
                ['id_curso' => $idCurso, 'id_curso_prerequisito' => $idPrereq],
                ['nota_minima' => 11.00, 'tipo' => 'Obligatorio', 'condicion' => 'Aprobado']
            );
        }

        // ══════════════════════════════════════════════════════════════════
        // 12. AULAS
        // ══════════════════════════════════════════════════════════════════
        $aula1 = Aula::create(['codigo_aula' => 'A-101', 'capacidad' => 40, 'ubicacion' => 'Pabellón A, Piso 1', 'tipo' => 'Teórica',      'estado' => 'Disponible']);
        $aula2 = Aula::create(['codigo_aula' => 'A-102', 'capacidad' => 35, 'ubicacion' => 'Pabellón A, Piso 1', 'tipo' => 'Teórica',      'estado' => 'Disponible']);
        $aula3 = Aula::create(['codigo_aula' => 'B-201', 'capacidad' => 30, 'ubicacion' => 'Pabellón B, Piso 2', 'tipo' => 'Laboratorio',  'estado' => 'Disponible']);
        $aula4 = Aula::create(['codigo_aula' => 'B-202', 'capacidad' => 25, 'ubicacion' => 'Pabellón B, Piso 2', 'tipo' => 'Laboratorio',  'estado' => 'Disponible']);

        // ══════════════════════════════════════════════════════════════════
        // 13. GRUPOS (uno por curso de nivel 1 y 2 para el período actual)
        // ══════════════════════════════════════════════════════════════════
        //  Solo creamos grupos para CURSOS ACTIVOS en el período actual
        //  Nivel 1 y 2 — los de nivel 3 se abren cuando haya estudiantes con prereq.
        $groupsData = [
            // [curso, docente_idx, aula, grupo_num]
            [$c101, 0, $aula1, 1], // Cálculo I - Carlos
            [$c101, 1, $aula2, 2], // Cálculo I - Sofia (2do grupo)
            [$c102, 2, $aula1, 1], // Álgebra Lineal I - Luis
            [$c103, 3, $aula2, 1], // Geometría Analítica - Ana
            [$c104, 4, $aula3, 1], // Lógica Matemática - Roberto
            [$c105, 3, $aula2, 1], // Probabilidades I - Ana
            [$c106, 3, $aula4, 1], // Est. Descriptiva - Ana
            [$c201, 0, $aula1, 1], // Cálculo II - Carlos
            [$c202, 2, $aula2, 1], // Álgebra Lineal II - Luis
            [$c203, 0, $aula3, 1], // Ec. Diferenciales - Carlos
            [$c205, 3, $aula4, 1], // Probabilidades II - Ana
            [$c206, 3, $aula4, 1], // Est. Inferencial - Ana
            [$c207, 4, $aula3, 1], // Métodos Numéricos - Roberto
        ];

        $grupos = [];
        foreach ($groupsData as [$curso, $docIdx, $aula, $numGrupo]) {
            $grupos[] = Grupo::create([
                'id_curso'    => $curso->id_curso,
                'id_docente'  => $docentes[$docIdx]->id_docente,
                'id_aula'     => $aula->id_aula,
                'id_periodo'  => $periodo->id_periodo,
                'numero_grupo'=> $numGrupo,
                'cupo_maximo' => $aula->capacidad,
                'estado'      => 'Abierto',
            ]);
        }

        // ══════════════════════════════════════════════════════════════════
        // 14. HORARIOS
        // ══════════════════════════════════════════════════════════════════
        $horarios = [
            [$grupos[0]->id_grupo,  'Lunes',     '08:00', '10:00'],
            [$grupos[0]->id_grupo,  'Miércoles', '08:00', '10:00'],
            [$grupos[1]->id_grupo,  'Martes',    '14:00', '16:00'],
            [$grupos[1]->id_grupo,  'Jueves',    '14:00', '16:00'],
            [$grupos[2]->id_grupo,  'Lunes',     '10:00', '12:00'],
            [$grupos[2]->id_grupo,  'Miércoles', '10:00', '12:00'],
            [$grupos[3]->id_grupo,  'Martes',    '08:00', '10:00'],
            [$grupos[3]->id_grupo,  'Viernes',   '08:00', '10:00'],
            [$grupos[4]->id_grupo,  'Lunes',     '16:00', '18:00'],
            [$grupos[5]->id_grupo,  'Martes',    '10:00', '12:00'],
            [$grupos[5]->id_grupo,  'Jueves',    '10:00', '12:00'],
            [$grupos[6]->id_grupo,  'Lunes',     '14:00', '16:00'],
            [$grupos[6]->id_grupo,  'Miércoles', '14:00', '16:00'],
            [$grupos[7]->id_grupo,  'Lunes',     '08:00', '10:00'],
            [$grupos[7]->id_grupo,  'Viernes',   '08:00', '10:00'],
            [$grupos[8]->id_grupo,  'Martes',    '10:00', '12:00'],
            [$grupos[9]->id_grupo,  'Miércoles', '10:00', '12:00'],
            [$grupos[10]->id_grupo, 'Jueves',    '14:00', '16:00'],
            [$grupos[11]->id_grupo, 'Viernes',   '14:00', '16:00'],
            [$grupos[12]->id_grupo, 'Jueves',    '16:00', '18:00'],
        ];
        foreach ($horarios as [$idGrupo, $dia, $ini, $fin]) {
            Horario::create([
                'id_grupo'    => $idGrupo,
                'dia_semana'  => $dia,
                'hora_inicio' => $ini . ':00',
                'hora_fin'    => $fin . ':00',
            ]);
        }

        // ══════════════════════════════════════════════════════════════════
        // 15. CONCEPTOS DE PAGO
        // ══════════════════════════════════════════════════════════════════
        ConceptoPago::create(['codigo' => 'MAT-2026', 'nombre' => 'Matrícula Académica Regular', 'monto_base' => 150.00, 'tipo' => 'Único',     'es_obligatorio' => true,  'estado' => 'Activo']);
        ConceptoPago::create(['codigo' => 'PEN-2026', 'nombre' => 'Pensión Semestral',           'monto_base' => 450.00, 'tipo' => 'Recurrente','es_obligatorio' => true,  'estado' => 'Activo']);
        ConceptoPago::create(['codigo' => 'LAB-2026', 'nombre' => 'Derecho de Laboratorio',      'monto_base' => 80.00,  'tipo' => 'Único',     'es_obligatorio' => false, 'estado' => 'Activo']);
        ConceptoPago::create(['codigo' => 'CERT-01',  'nombre' => 'Certificado de Estudios',     'monto_base' => 30.00,  'tipo' => 'Único',     'es_obligatorio' => false, 'estado' => 'Activo']);

        // ══════════════════════════════════════════════════════════════════
        // 16. LOG INICIAL
        // ══════════════════════════════════════════════════════════════════
        LogActividad::create([
            'id_usuario'           => $adminUser->id_usuario,
            'accion'               => 'INSERT',
            'tabla_afectada'       => 'SISTEMA',
            'id_registro_afectado' => 1,
            'datos_anteriores'     => null,
            'datos_nuevos'         => 'Inicialización del sistema académico 2026-I',
            'fecha_hora'           => Carbon::now(),
            'direccion_ip'         => '127.0.0.1',
            'modulo'               => 'Sistema',
        ]);

        // ══════════════════════════════════════════════════════════════════
        // 17. ESTUDIANTES DE PRUEBA E HISTORIAL (PERIODO ANTERIOR Y ACTUAL)
        // ══════════════════════════════════════════════════════════════════
        $periodoAnterior = PeriodoAcademico::create([
            'codigo' => '2025-II', 'nombre' => 'Periodo Académico 2025 – II',
            'fecha_inicio' => '2025-08-01', 'fecha_fin' => '2025-12-15',
            'tipo' => 'Regular', 'estado' => 'Finalizado',
        ]);

        $grupoAnterior = Grupo::create([
            'id_curso'    => $c101->id_curso,
            'id_docente'  => $docentes[0]->id_docente,
            'id_aula'     => $aula1->id_aula,
            'id_periodo'  => $periodoAnterior->id_periodo,
            'numero_grupo'=> 1,
            'cupo_maximo' => $aula1->capacidad,
            'estado'      => 'Cerrado',
        ]);

        $nombres = ['Juan', 'Maria', 'Carlos', 'Luis', 'Ana', 'Jose', 'Pedro', 'Lucia', 'Sofia', 'Miguel', 'Diego', 'Jorge', 'Elena', 'Carmen', 'Rosa', 'Andres', 'Valeria', 'Daniel'];
        $apellidos = ['Perez', 'Gomez', 'Lopez', 'Garcia', 'Fernandez', 'Martinez', 'Rodriguez', 'Sanchez', 'Ramirez', 'Torres', 'Flores', 'Vargas', 'Rios', 'Castro', 'Ortiz', 'Silva'];

        for ($i = 1; $i <= 60; $i++) {
            $nombre = $nombres[array_rand($nombres)];
            $apellido = $apellidos[array_rand($apellidos)] . ' ' . $apellidos[array_rand($apellidos)];
            $dni = '7000' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $email = strtolower($nombre . '.' . explode(' ', $apellido)[0] . $i . '@test.com');

            $p = Persona::create([
                'tipo_documento' => 'DNI', 'numero_documento' => $dni,
                'nombres' => $nombre, 'apellidos' => $apellido,
                'fecha_nacimiento' => Carbon::now()->subYears(rand(18, 25))->format('Y-m-d'),
                'direccion' => 'Avenida Principal ' . rand(100, 999),
                'telefono' => '999888' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'email' => $email,
            ]);
            
            $estudiante = \App\Models\Estudiante::create([
                'id_estudiante' => $p->id_persona,
                'codigo_estudiante' => 'EST-2026-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'fecha_ingreso' => '2025-08-01',
                'estado' => 'Activo'
            ]);

            $tipo = $i % 4; // 0 = Nuevo, 1 = Aprobado periodo anterior, 2 = Desaprobado periodo anterior, 3 = Inscrito actual
            $pagoStatus = ($i % 3 === 0) ? 'Pendiente' : 'Pagado'; // 1/3 Pendiente, 2/3 Pagado

            if ($tipo === 3) {
                $pagoStatus = 'Pagado'; // Si ya está inscrito, tuvo que haber pagado
            }

            // Generar deuda para el periodo ACTUAL (2026-I)
            $deuda = \App\Models\DeudaEstudiante::create([
                'id_estudiante' => $estudiante->id_estudiante,
                'id_concepto' => 1, // Matrícula Académica Regular
                'id_periodo' => $periodo->id_periodo,
                'monto' => 150.00,
                'estado' => $pagoStatus
            ]);

            if ($pagoStatus === 'Pagado') {
                $pago = \App\Models\Pago::create([
                    'id_estudiante' => $estudiante->id_estudiante,
                    'id_usuario_registra' => $cajUser->id_usuario,
                    'id_sesion_caja' => $sesionAbierta->id_sesion_caja,
                    'numero_comprobante' => 'BOL-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'tipo_comprobante' => 'Boleta',
                    'monto_total' => 150.00,
                    'metodo_pago' => 'Transferencia Bancaria',
                    'estado' => 'Registrado',
                    'fecha_pago' => Carbon::now()->subDays(rand(1, 10))
                ]);
                
                \App\Models\DetallePago::create([
                    'id_pago' => $pago->id_pago,
                    'id_concepto' => 1,
                    'monto_aplicado' => 150.00,
                    'descripcion' => 'Pago de matrícula regular'
                ]);
            }

            if ($tipo === 1 || $tipo === 2) {
                // Historial en periodo anterior
                $matricula = \App\Models\Matricula::create([
                    'id_estudiante' => $estudiante->id_estudiante,
                    'id_periodo' => $periodoAnterior->id_periodo,
                    'id_especialidad' => $espMat->id_especialidad,
                    'fecha_matricula' => '2025-08-05',
                    'tipo' => 'Regular',
                    'estado' => 'Registrada',
                    'id_usuario_registra' => $secUser->id_usuario,
                ]);

                $inscripcion = \App\Models\Inscripcion::create([
                    'id_matricula' => $matricula->id_matricula,
                    'fecha_inscripcion' => '2025-08-05',
                    'estado' => 'Confirmada',
                    'id_usuario_registra' => $secUser->id_usuario,
                ]);

                $detalleInscripcion = \App\Models\DetalleInscripcion::create([
                    'id_inscripcion' => $inscripcion->id_inscripcion,
                    'id_grupo' => $grupoAnterior->id_grupo,
                    'estado' => 'Registrado'
                ]);

                \App\Models\NotaFinal::create([
                    'id_detalle_inscripcion' => $detalleInscripcion->id_detalle_inscripcion,
                    'nota' => $tipo === 1 ? mt_rand(12, 20) : mt_rand(5, 10),
                    'estado' => $tipo === 1 ? 'Aprobado' : 'Desaprobado',
                    'id_usuario_registra' => $docentesUsers[0]->id_usuario,
                    'fecha_registro' => '2025-12-16',
                ]);
            } else if ($tipo === 3) {
                // En curso en periodo actual
                $matricula = \App\Models\Matricula::create([
                    'id_estudiante' => $estudiante->id_estudiante,
                    'id_periodo' => $periodo->id_periodo,
                    'id_especialidad' => $espMat->id_especialidad,
                    'fecha_matricula' => Carbon::now()->subDays(rand(5, 20)),
                    'tipo' => 'Regular',
                    'estado' => 'Activa',
                    'id_usuario_registra' => $secUser->id_usuario,
                ]);

                $inscripcion = \App\Models\Inscripcion::create([
                    'id_matricula' => $matricula->id_matricula,
                    'fecha_inscripcion' => Carbon::now()->subDays(rand(1, 4)),
                    'estado' => 'Confirmada',
                    'id_usuario_registra' => $secUser->id_usuario,
                ]);

                // Lo inscribimos en un curso de Nivel 1 aleatorio (índices 0 al 6)
                \App\Models\DetalleInscripcion::create([
                    'id_inscripcion' => $inscripcion->id_inscripcion,
                    'id_grupo' => $grupos[rand(0, 6)]->id_grupo,
                    'estado' => 'Inscrito'
                ]);
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
