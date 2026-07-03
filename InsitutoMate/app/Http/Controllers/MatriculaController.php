<?php

namespace App\Http\Controllers;

use App\Models\Matricula;
use App\Models\Inscripcion;
use App\Models\DetalleInscripcion;
use App\Models\Estudiante;
use App\Models\PeriodoAcademico;
use App\Models\Especialidad;
use App\Models\Grupo;
use App\Models\Pago;
use App\Models\NotaFinal;
use App\Models\CursoPrerrequisito;
use App\Models\LogActividad;
use App\Models\DeudaEstudiante;
use App\Models\ConceptoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MatriculaController extends Controller
{
    /**
     * Display a listing of matriculas.
     */
    public function index()
    {
        $matriculas = Matricula::with(['estudiante.persona', 'periodo', 'especialidad', 'usuario.persona'])->get();

        // Data for registration form
        $estudiantes = Estudiante::with('persona')->where('estado', 'Activo')->get();

        $periodos = PeriodoAcademico::where('estado', 'En Curso')->get();
        $especialidades = Especialidad::where('estado', 'Activa')->get();
        $grupos = Grupo::with(['curso.prerrequisitos.cursoPrerrequisito', 'docente.persona', 'aula'])
            ->where('estado', 'Abierto')
            ->get();

        return view('matriculas.index', compact('matriculas', 'estudiantes', 'periodos', 'especialidades', 'grupos'));
    }

    /**
     * Check if a student has no past debts and has paid the current matricula.
     * Returns JSON for AJAX calls from the form.
     */
    public function verificarPago(Request $request, $id_estudiante, $id_periodo)
    {
        // 1. Verificar deudas pasadas o actuales
        $deudasPendientes = DeudaEstudiante::where('id_estudiante', $id_estudiante)
            ->where('estado', 'Pendiente')
            ->exists();

        if ($deudasPendientes) {
            return response()->json([
                'tiene_pago' => false,
                'mensaje_error' => 'El estudiante mantiene DEUDAS PENDIENTES. Debe cancelarlas en Caja para poder matricularse en una nueva materia.'
            ]);
        }

        // Ya que bloqueamos cualquier deuda pendiente arriba, 
        // si llega hasta aquí es porque no tiene deudas pendientes.
        // Pero debemos asegurarnos de que NO se salte pagos.
        // REGLA: Debe tener (Inscripciones Actuales + 1) Matrículas Pagadas para poder inscribirse en un nuevo curso.
        // ESTO SOLO APLICA A ESTUDIANTES DE 'Especialidad'.
        $est = \App\Models\Estudiante::find($id_estudiante);
        if ($est) {
            // Lógica para Especialidad
            if ($est->modalidad_estudio === 'Especialidad') {
                // Verificar que tenga pagada la mensualidad correspondiente a la nueva materia
                $cMensualidad = ConceptoPago::where('nombre', 'like', '%Mensualidad%')->orWhere('nombre', 'like', '%Pensión%')->first();
                if ($cMensualidad) {
                    $totalInscripciones = \App\Models\DetalleInscripcion::whereHas('inscripcion.matricula', function ($q) use ($id_estudiante) {
                        $q->where('id_estudiante', $id_estudiante);
                    })->where('estado', '!=', 'Retirado')->count();

                    $totalMensualidadesPagadas = DeudaEstudiante::where('id_estudiante', $id_estudiante)
                        ->where('id_concepto', $cMensualidad->id_concepto)
                        ->where('estado', 'Pagado')
                        ->count();

                    if ($totalMensualidadesPagadas <= $totalInscripciones) {
                        // Auto-generar deuda si no existe una pendiente
                        $hasPending = DeudaEstudiante::where('id_estudiante', $id_estudiante)
                            ->where('id_concepto', $cMensualidad->id_concepto)
                            ->where('estado', 'Pendiente')->exists();
                            
                        if (!$hasPending) {
                            DeudaEstudiante::create([
                                'id_estudiante' => $id_estudiante,
                                'id_periodo' => $id_periodo,
                                'id_concepto' => $cMensualidad->id_concepto,
                                'monto' => $cMensualidad->monto_base ?? 250.00,
                                'estado' => 'Pendiente',
                                'fecha_generacion' => Carbon::now()
                            ]);
                        }
                        
                        return response()->json([
                            'tiene_pago' => false,
                            'mensaje_error' => 'Falta pagar la MENSUALIDAD del nuevo curso. Se ha generado la deuda en el sistema. Por favor, cancele en Caja para habilitar la inscripción.'
                        ]);
                    }
                }
            }
            
            // Lógica para Curso Libre
            if ($est->modalidad_estudio === 'Curso Libre') {
                $cLibre = ConceptoPago::where('nombre', 'like', 'Curso Libre%')->first();
                if ($cLibre) {
                    $totalInscripciones = \App\Models\DetalleInscripcion::whereHas('inscripcion.matricula', function ($q) use ($id_estudiante) {
                        $q->where('id_estudiante', $id_estudiante);
                    })->where('estado', '!=', 'Retirado')->count();

                    $totalCursosPagados = DeudaEstudiante::where('id_estudiante', $id_estudiante)
                        ->where('id_concepto', $cLibre->id_concepto)
                        ->where('estado', 'Pagado')
                        ->count();

                    if ($totalCursosPagados <= $totalInscripciones) {
                        // Auto-generar deuda
                        $hasPending = DeudaEstudiante::where('id_estudiante', $id_estudiante)
                            ->where('id_concepto', $cLibre->id_concepto)
                            ->where('estado', 'Pendiente')->exists();
                            
                        if (!$hasPending) {
                            DeudaEstudiante::create([
                                'id_estudiante' => $id_estudiante,
                                'id_periodo' => $id_periodo,
                                'id_concepto' => $cLibre->id_concepto,
                                'monto' => $cLibre->monto_base ?? 300.00,
                                'estado' => 'Pendiente',
                                'fecha_generacion' => Carbon::now()
                            ]);
                        }
                        
                        return response()->json([
                            'tiene_pago' => false,
                            'mensaje_error' => 'Falta pagar el CURSO LIBRE. Se ha generado la deuda (300 Bs). Por favor, cancele en Caja para habilitar la inscripción.'
                        ]);
                    }
                }
            }
        }

        // Todo en orden (sin deudas pasadas y matrícula actual pagada)
        return response()->json([
            'tiene_pago' => true,
            'mensaje_exito' => 'Solvencia verificada. Estudiante apto para inscripción.'
        ]);
    }

    /**
     * Obtener grupos disponibles para el estudiante (filtrado por prerrequisitos y cursos en progreso).
     */
    public function obtenerGruposDisponibles($id_estudiante, $id_periodo, $id_especialidad)
    {
        // 1. Verificar si tiene cursos en progreso (sin nota final)
        $cursosEnProgreso = DetalleInscripcion::whereHas('inscripcion.matricula', function ($q) use ($id_estudiante) {
                $q->where('id_estudiante', $id_estudiante);
            })
            ->where('estado', '!=', 'Retirado')
            ->doesntHave('notaFinal')
            ->with('grupo.curso')
            ->first();

        if ($cursosEnProgreso) {
            return response()->json([
                'success' => false,
                'mensaje' => 'El estudiante tiene el curso «' . optional(optional($cursosEnProgreso->grupo)->curso)->nombre_curso . '» en progreso. Debe finalizarlo para inscribir otro.'
            ]);
        }

        // 2. Obtener IDs de cursos que ya aprobó
        $cursosAprobadosIds = NotaFinal::where('estado', 'Aprobado')
            ->where('nota', '>=', 11.00)
            ->whereHas('detalleInscripcion.inscripcion.matricula', function($q) use ($id_estudiante) {
                $q->where('id_estudiante', $id_estudiante);
            })
            ->get()
            ->map(function($n) {
                return optional(optional($n->detalleInscripcion)->grupo)->id_curso;
            })
            ->filter()
            ->unique()
            ->toArray();

        // 2.5. Verificar si tiene cursos reprobados que AÚN no ha aprobado
        $cursosDesaprobadosIds = NotaFinal::where('estado', 'Desaprobado')
            ->whereHas('detalleInscripcion.inscripcion.matricula', function($q) use ($id_estudiante) {
                $q->where('id_estudiante', $id_estudiante);
            })
            ->get()
            ->map(function($n) {
                return optional(optional($n->detalleInscripcion)->grupo)->id_curso;
            })
            ->filter()
            ->unique()
            ->toArray();

        $cursosARepetir = [];
        foreach ($cursosDesaprobadosIds as $idDesaprobado) {
            if (!in_array($idDesaprobado, $cursosAprobadosIds)) {
                $cursosARepetir[] = $idDesaprobado;
            }
        }
        $debeRepetir = count($cursosARepetir) > 0;

        $estudiante = \App\Models\Estudiante::find($id_estudiante);
        $esLibre = $estudiante && $estudiante->modalidad_estudio === 'Curso Libre';

        // 3. Obtener grupos abiertos o en pre-inscripción para el periodo
        $query = Grupo::with(['curso.prerrequisitos', 'docente.persona', 'horarios'])
            ->where('id_periodo', $id_periodo)
            ->whereIn('estado', ['Abierto', 'Pre-Inscripción']);

        // Si NO es libre, filtramos estrictamente por especialidad
        if (!$esLibre) {
            $query->whereHas('curso', function($q) use ($id_especialidad) {
                $q->where('id_especialidad', $id_especialidad)
                  ->where('estado', 'Activo');
            });
        }

        $gruposAbiertos = $query->get();

        $gruposDisponibles = [];

        foreach ($gruposAbiertos as $grupo) {
            $curso = $grupo->curso;
            
            // Si ya lo aprobó, no mostrarlo
            if (in_array($curso->id_curso, $cursosAprobadosIds)) {
                continue;
            }

            // REGLA: Si debe repetir un curso, SOLO puede ver los cursos que debe repetir
            if ($debeRepetir && !in_array($curso->id_curso, $cursosARepetir)) {
                continue;
            }

            // Verificar si cumple todos los prerrequisitos (si no está repitiendo y no es Curso Libre)
            $cumplePrerrequisitos = true;
            if (!$debeRepetir && !$esLibre) {
                foreach ($curso->prerrequisitos as $prereq) {
                    if (!in_array($prereq->id_curso_prerequisito, $cursosAprobadosIds)) {
                        $cumplePrerrequisitos = false;
                        break;
                    }
                }
            }

            if ($cumplePrerrequisitos) {
                $turno = "Sin Turno";
                if ($grupo->horarios && $grupo->horarios->count() > 0) {
                    $horaInicio = substr($grupo->horarios[0]->hora_inicio, 0, 5);
                    if ($horaInicio === '08:30') $turno = 'Mañana';
                    elseif ($horaInicio === '11:30') $turno = 'Medio Día';
                    elseif ($horaInicio === '14:30') $turno = 'Tarde';
                    elseif ($horaInicio === '19:30') $turno = 'Noche';
                }

                $gruposDisponibles[] = [
                    'id_grupo' => $grupo->id_grupo,
                    'codigo_curso' => $curso->codigo_curso,
                    'nombre_curso' => $curso->nombre_curso,
                    'numero_grupo' => $grupo->numero_grupo,
                    'docente' => optional(optional($grupo->docente)->persona)->nombres,
                    'tiene_prerreq' => $curso->prerrequisitos->count() > 0,
                    'turno' => $turno
                ];
            }
        }

        if (count($gruposDisponibles) > 0) {
            return response()->json([
                'success' => true,
                'grupos' => $gruposDisponibles
            ]);
        }

        return response()->json([
            'success' => false,
            'mensaje' => 'No hay grupos disponibles (ya completó la currícula, faltan prerrequisitos, o no hay grupos abiertos).'
        ]);
    }

    /**
     * Verify prerequisite details for a student and a group/course.
     */
    public function verificarPrerrequisito($id_estudiante, $id_grupo)
    {
        $grupo = Grupo::with('curso')->findOrFail($id_grupo);
        $curso = $grupo->curso;

        $prerrequisitos = CursoPrerrequisito::where('id_curso', $curso->id_curso)
            ->with('cursoPrerrequisito')
            ->get();

        $detallePrereqs = [];
        $cumpleTodos = true;

        foreach ($prerrequisitos as $prereq) {
            $notaFinal = NotaFinal::whereHas('detalleInscripcion.grupo', function ($q) use ($prereq) {
                    $q->where('id_curso', $prereq->id_curso_prerequisito);
                })
                ->whereHas('detalleInscripcion.inscripcion.matricula', function ($q) use ($id_estudiante) {
                    $q->where('id_estudiante', $id_estudiante);
                })
                ->orderBy('nota', 'desc')
                ->first();

            $aprobado = $notaFinal && $notaFinal->estado === 'Aprobado' && $notaFinal->nota >= $prereq->nota_minima;

            if (!$aprobado) {
                $cumpleTodos = false;
            }

            $detallePrereqs[] = [
                'nombre_curso' => $prereq->cursoPrerrequisito->nombre_curso,
                'nota_minima' => (float)$prereq->nota_minima,
                'nota_obtenida' => $notaFinal ? (float)$notaFinal->nota : null,
                'estado' => $notaFinal ? $notaFinal->estado : 'No Cursado',
                'cumple' => $aprobado
            ];
        }

        return response()->json([
            'cumple_todos' => $cumpleTodos,
            'prerrequisitos' => $detallePrereqs
        ]);
    }

    /**
     * Store a newly created matricula and enrollment in storage.
     * Validates: 1) Payment of "Matrícula" exists. 2) Course prerequisites are met.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_estudiante'  => 'required|integer|exists:ESTUDIANTE,id_estudiante',
            'id_periodo'     => 'required|integer|exists:PERIODO_ACADEMICO,id_periodo',
            'id_especialidad'=> 'required|integer|exists:ESPECIALIDAD,id_especialidad',
            'id_grupo'       => 'required|integer|exists:GRUPO,id_grupo',
            'tipo'           => 'required|string|in:Regular,Reincorporación,Traslado',
            'observaciones'  => 'nullable|string|max:300',
        ]);

        // ─────────────────────────────────────────────────────────────────
        // VALIDACIÓN 0: Verificar si el estudiante tiene cursos en curso (sin nota final)
        // ─────────────────────────────────────────────────────────────────
        $cursosEnProgreso = DetalleInscripcion::whereHas('inscripcion.matricula', function ($q) use ($request) {
                $q->where('id_estudiante', $request->id_estudiante);
            })
            ->where('estado', '!=', 'Retirado')
            ->doesntHave('notaFinal')
            ->with('grupo.curso')
            ->first();

        if ($cursosEnProgreso) {
            $cursoNombre = optional(optional($cursosEnProgreso->grupo)->curso)->nombre_curso ?? 'Desconocido';
            return back()->withErrors([
                'en_progreso' => '⚠️ El estudiante tiene el curso «' . $cursoNombre . '» en progreso. Debe finalizarlo (tener nota) antes de registrar una nueva materia.',
            ])->withInput();
        }

        // ─────────────────────────────────────────────────────────────────
        // VALIDACIÓN 1: El estudiante no debe tener NINGUNA deuda pendiente
        // ─────────────────────────────────────────────────────────────────
        $deudasPendientes = DeudaEstudiante::where('id_estudiante', $request->id_estudiante)
            ->where('estado', 'Pendiente')
            ->exists();

        if ($deudasPendientes) {
            return back()->withErrors([
                'deuda' => '⚠️ El estudiante tiene deudas pendientes (Pensión o Matrícula). Debe cancelar en Caja antes de inscribir una nueva materia.',
            ])->withInput();
        }

        // Validamos la cantidad exacta de matrículas vs inscripciones (SOLO PARA ESPECIALIDAD)
        $estudiante = Estudiante::findOrFail($request->id_estudiante);
        if ($estudiante->modalidad_estudio === 'Especialidad') {
            $cMatricula = ConceptoPago::where('nombre', 'like', 'Matrícula%')->first();
            if ($cMatricula) {
                $totalInscripciones = \App\Models\DetalleInscripcion::whereHas('inscripcion.matricula', function ($q) use ($request) {
                    $q->where('id_estudiante', $request->id_estudiante);
                })->where('estado', '!=', 'Retirado')->count();

                $totalMatriculasPagadas = DeudaEstudiante::where('id_estudiante', $request->id_estudiante)
                    ->where('id_concepto', $cMatricula->id_concepto)
                    ->where('estado', 'Pagado')
                    ->count();

                if ($totalMatriculasPagadas <= $totalInscripciones) {
                    return back()->withErrors([
                        'pago' => '⚠️ Intento de salto de pago: El estudiante tiene ' . $totalInscripciones . ' inscripciones previas y ' . $totalMatriculasPagadas . ' matrículas pagadas. Falta el pago en Caja para esta nueva materia.',
                    ])->withInput();
                }
            }
        }

        // ─────────────────────────────────────────────────────────────────
        // VALIDACIÓN 1.5: Verificación estricta de cursos reprobados
        // ─────────────────────────────────────────────────────────────────
        $grupo = Grupo::with('curso')->findOrFail($request->id_grupo);
        $curso = $grupo->curso;

        $cursosAprobadosIds = NotaFinal::where('estado', 'Aprobado')
            ->where('nota', '>=', 11.00)
            ->whereHas('detalleInscripcion.inscripcion.matricula', function($q) use ($request) {
                $q->where('id_estudiante', $request->id_estudiante);
            })
            ->get()
            ->map(function($n) { return optional(optional($n->detalleInscripcion)->grupo)->id_curso; })
            ->filter()->unique()->toArray();

        if (in_array($curso->id_curso, $cursosAprobadosIds)) {
            return back()->withErrors([
                'curso_aprobado' => '⚠️ El estudiante ya aprobó el curso «' . $curso->nombre_curso . '».',
            ])->withInput();
        }

        $cursosDesaprobadosIds = NotaFinal::where('estado', 'Desaprobado')
            ->whereHas('detalleInscripcion.inscripcion.matricula', function($q) use ($request) {
                $q->where('id_estudiante', $request->id_estudiante);
            })
            ->get()
            ->map(function($n) { return optional(optional($n->detalleInscripcion)->grupo)->id_curso; })
            ->filter()->unique()->toArray();

        $cursosARepetir = [];
        foreach ($cursosDesaprobadosIds as $idDesaprobado) {
            if (!in_array($idDesaprobado, $cursosAprobadosIds)) {
                $cursosARepetir[] = $idDesaprobado;
            }
        }

        if (count($cursosARepetir) > 0 && !in_array($curso->id_curso, $cursosARepetir)) {
            return back()->withErrors([
                'curso_reprobado' => '⚠️ Acción bloqueada: El estudiante tiene materias reprobadas. Está OBLIGADO a repetir la materia desaprobada antes de poder cursar otras nuevas.',
            ])->withInput();
        }

        // ─────────────────────────────────────────────────────────────────
        // VALIDACIÓN 2: Verificar PRERREQUISITOS del curso seleccionado
        // ─────────────────────────────────────────────────────────────────
        $grupo = Grupo::with('curso')->findOrFail($request->id_grupo);
        $curso = $grupo->curso;

        $prerrequisitos = CursoPrerrequisito::where('id_curso', $curso->id_curso)
            ->with('cursoPrerrequisito')
            ->get();

        $faltantes = [];
        foreach ($prerrequisitos as $prereq) {
            // Check if student has an APPROVED nota in the prerequisite course
            $aprobado = NotaFinal::where('estado', 'Aprobado')
                ->where('nota', '>=', $prereq->nota_minima)
                ->whereHas('detalleInscripcion.grupo', function ($q) use ($prereq) {
                    $q->where('id_curso', $prereq->id_curso_prerequisito);
                })
                ->whereHas('detalleInscripcion.inscripcion.matricula', function ($q) use ($request) {
                    $q->where('id_estudiante', $request->id_estudiante);
                })
                ->exists();

            if (!$aprobado) {
                $faltantes[] = '«' . $prereq->cursoPrerrequisito->nombre_curso . '» (nota mínima: ' . number_format($prereq->nota_minima, 2) . ')';
            }
        }

        if (!empty($faltantes)) {
            return back()->withErrors([
                'prereq' => '📚 No se puede inscribir en «' . $curso->nombre_curso . '». Falta aprobar: ' . implode(', ', $faltantes) . '.',
            ])->withInput();
        }

        // ─────────────────────────────────────────────────────────────────
        // VALIDACIÓN 3: Evitar inscripción duplicada en el mismo curso
        // ─────────────────────────────────────────────────────────────────
        $yaInscrito = DetalleInscripcion::whereHas('inscripcion.matricula', function ($q) use ($request) {
            $q->where('id_estudiante', $request->id_estudiante)
              ->where('id_periodo', $request->id_periodo);
        })
        ->whereHas('grupo', function ($q) use ($curso) {
            $q->where('id_curso', $curso->id_curso);
        })
        ->where('estado', '!=', 'Retirado')
        ->exists();

        if ($yaInscrito) {
            return back()->withErrors([
                'duplicado' => '⚠️ El estudiante ya se encuentra inscrito en el curso «' . $curso->nombre_curso . '» en el periodo actual.',
            ])->withInput();
        }

        // ─────────────────────────────────────────────────────────────────
        // PROCESO: Crear Matrícula → Inscripción → Detalle
        // ─────────────────────────────────────────────────────────────────
        try {
            DB::transaction(function () use ($request, $curso, $grupo) {
                // Check if there is already a matrícula for this student+period
                $matriculaExistente = Matricula::where('id_estudiante', $request->id_estudiante)
                    ->where('id_periodo', $request->id_periodo)
                    ->first();

                if ($matriculaExistente) {
                    // Reuse existing matrícula, just add a new Inscripción
                    $matricula = $matriculaExistente;
                } else {
                    $matricula = Matricula::create([
                        'id_estudiante'      => $request->id_estudiante,
                        'id_periodo'         => $request->id_periodo,
                        'id_especialidad'    => $request->id_especialidad,
                        'fecha_matricula'    => Carbon::now(),
                        'tipo'               => $request->tipo,
                        'estado'             => 'Activa',
                        'observaciones'      => $request->observaciones,
                        'id_usuario_registra'=> Auth::id(),
                    ]);
                }

                // Create Inscripcion
                $inscripcion = Inscripcion::create([
                    'id_matricula'       => $matricula->id_matricula,
                    'fecha_inscripcion'  => Carbon::now(),
                    'estado'             => 'Activa',
                    'id_usuario_registra'=> Auth::id(),
                ]);

                // Create DetalleInscripcion
                $detalle = DetalleInscripcion::create([
                    'id_inscripcion' => $inscripcion->id_inscripcion,
                    'id_grupo'       => $request->id_grupo,
                    'estado'         => 'Inscrito',
                ]);

                // Check if group has reached 15 students and is in Pre-Inscripción
                if ($grupo->estado === 'Pre-Inscripción') {
                    $count = DetalleInscripcion::where('id_grupo', $grupo->id_grupo)
                        ->where('estado', '!=', 'Retirado')
                        ->count();
                    if ($count >= 15) {
                        $grupo->estado = 'Abierto';
                        $grupo->save();
                    }
                }

                // Generar deuda de Laboratorio si aplica
                if ($grupo->aula && $grupo->aula->tipo === 'Laboratorio') {
                    $cLab = ConceptoPago::where('nombre', 'like', 'Derecho de Laboratorio%')->first();
                    if ($cLab) {
                        DeudaEstudiante::create([
                            'id_estudiante' => $request->id_estudiante,
                            'id_periodo' => $request->id_periodo,
                            'id_concepto' => $cLab->id_concepto,
                            'monto' => $cLab->monto_base ?? 80.00,
                            'estado' => 'Pendiente',
                            'fecha_generacion' => Carbon::now()
                        ]);
                    }
                }

                // Log Activity
                LogActividad::create([
                    'id_usuario'           => Auth::id(),
                    'accion'               => 'INSERT',
                    'tabla_afectada'       => 'MATRICULA',
                    'id_registro_afectado' => $matricula->id_matricula,
                    'datos_anteriores'     => null,
                    'datos_nuevos'         => json_encode([
                        'curso'      => $curso->nombre_curso,
                        'matricula'  => $matricula->toArray(),
                        'inscripcion'=> $inscripcion->toArray(),
                        'detalle'    => $detalle->toArray(),
                    ]),
                    'fecha_hora'   => Carbon::now(),
                    'direccion_ip' => request()->ip(),
                    'modulo'       => 'Matrícula',
                ]);
            });

            return redirect()->route('matriculas.index')
                ->with('success', '✅ Inscripción en «' . $curso->nombre_curso . '» procesada correctamente.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al procesar la inscripción: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified matricula from storage.
     */
    public function destroy($id)
    {
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'No autorizado.');
        }

        try {
            DB::transaction(function () use ($id) {
                $matricula = Matricula::findOrFail($id);
                $matricula->delete();

                LogActividad::create([
                    'id_usuario' => Auth::id(),
                    'accion' => 'DELETE',
                    'tabla_afectada' => 'MATRICULA',
                    'id_registro_afectado' => $id,
                    'fecha_hora' => Carbon::now(),
                    'direccion_ip' => request()->ip(),
                    'modulo' => 'Matrícula',
                ]);
            });

            return redirect()->route('matriculas.index')->with('success', 'Matrícula eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar la matrícula: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $matricula = Matricula::findOrFail($id);

        $request->validate([
            'tipo' => 'required|string|in:Regular,Reincorporación,Traslado',
            'observaciones' => 'nullable|string|max:300',
        ]);

        try {
            DB::transaction(function () use ($request, $matricula) {
                $oldData = $matricula->toArray();
                
                $matricula->update([
                    'tipo' => $request->tipo,
                    'observaciones' => $request->observaciones,
                ]);

                LogActividad::create([
                    'id_usuario' => Auth::id(),
                    'accion' => 'UPDATE',
                    'tabla_afectada' => 'MATRICULA',
                    'id_registro_afectado' => $matricula->id_matricula,
                    'datos_anteriores' => json_encode($oldData),
                    'datos_nuevos' => json_encode($matricula->toArray()),
                    'fecha_hora' => Carbon::now(),
                    'direccion_ip' => request()->ip(),
                    'modulo' => 'Matrícula',
                ]);
            });

            return redirect()->route('matriculas.index')->with('success', 'Matrícula actualizada correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar la matrícula: ' . $e->getMessage()]);
        }
    }
}
