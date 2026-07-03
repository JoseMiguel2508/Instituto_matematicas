<?php

namespace App\Http\Controllers;

use App\Models\DetalleInscripcion;
use App\Models\NotaFinal;
use App\Models\LogActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // 1. Obtener la lista de grupos disponibles para el dropdown
        $gruposQuery = \App\Models\Grupo::with(['curso', 'periodo']);
        if ($user->hasRole('Docente') && !$user->hasAnyRole(['Administrador', 'Coordinador'])) {
            $gruposQuery->where('id_docente', $user->id_persona);
        }
        $availableGroups = $gruposQuery->orderBy('id_periodo', 'desc')->get();

        // 2. Si se seleccionó un grupo, mostrar los alumnos de ese grupo
        $grupos = collect();
        if ($request->has('id_grupo') && $request->id_grupo != '') {
            $query = DetalleInscripcion::with([
                'inscripcion.matricula.estudiante.persona',
                'grupo.curso',
                'grupo.docente.persona',
                'grupo.horarios',
                'notaFinal.usuario.persona'
            ])->where('id_grupo', $request->input('id_grupo'));

            $inscritos = $query->get();
            $grupos = $inscritos->groupBy('id_grupo');
        }

        return view('notas.index', compact('grupos', 'availableGroups'));
    }

    /**
     * Store or update a final grade for an enrollment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_detalle_inscripcion' => 'required|integer|exists:DETALLE_INSCRIPCION,id_detalle_inscripcion',
            'nota' => 'required|numeric|min:0|max:20',
            'observaciones' => 'nullable|string|max:200',
        ]);

        $detalle = DetalleInscripcion::with('grupo.periodo')->findOrFail($request->id_detalle_inscripcion);
        
        if ($detalle->grupo->estado !== 'Abierto') {
            return back()->withErrors(['error' => 'No se pueden registrar notas. El grupo no se encuentra en estado "Abierto" (tiene menos de 15 estudiantes o está cancelado).']);
        }

        $fechaFin = $detalle->grupo->periodo->fecha_fin;
        
        if (Carbon::now()->startOfDay()->lt(Carbon::parse($fechaFin)->startOfDay())) {
            return back()->withErrors(['error' => 'Aún no puedes registrar notas. El periodo académico finaliza el ' . Carbon::parse($fechaFin)->format('d/m/Y') . '.']);
        }

        $notaVal = (float)$request->nota;
        $estado = $notaVal >= 11.00 ? 'Aprobado' : 'Desaprobado';

        try {
            DB::transaction(function () use ($request, $notaVal, $estado, $detalle) {
                // Find if grade exists for this detail
                $notaFinal = NotaFinal::where('id_detalle_inscripcion', $request->id_detalle_inscripcion)->first();
                $action = 'INSERT';
                $oldData = null;

                if ($notaFinal) {
                    $action = 'UPDATE';
                    $oldData = json_encode($notaFinal->toArray());

                    $notaFinal->update([
                        'nota' => $notaVal,
                        'estado' => $estado,
                        'id_usuario_registra' => Auth::id(),
                        'fecha_registro' => Carbon::now(),
                        'observaciones' => $request->observaciones,
                    ]);
                } else {
                    $notaFinal = NotaFinal::create([
                        'id_detalle_inscripcion' => $request->id_detalle_inscripcion,
                        'nota' => $notaVal,
                        'estado' => $estado,
                        'id_usuario_registra' => Auth::id(),
                        'fecha_registro' => Carbon::now(),
                        'observaciones' => $request->observaciones,
                    ]);

                    // Generar nueva deuda de Matrícula para la siguiente inscripción
                    $estudianteId = $detalle->inscripcion->matricula->id_estudiante;
                    $periodoActual = \App\Models\PeriodoAcademico::where('estado', 'En Curso')->first();
                    $idPeriodo = $periodoActual ? $periodoActual->id_periodo : $detalle->grupo->id_periodo;
                    
                    $cMatricula = \App\Models\ConceptoPago::where('nombre', 'like', 'Matrícula%')->first();
                    if ($cMatricula) {
                        \App\Models\DeudaEstudiante::create([
                            'id_estudiante' => $estudianteId,
                            'id_periodo' => $idPeriodo,
                            'id_concepto' => $cMatricula->id_concepto,
                            'monto' => $cMatricula->monto_base ?? 150.00,
                            'estado' => 'Pendiente',
                            'fecha_generacion' => Carbon::now()
                        ]);
                    }
                }

                // Log Activity
                LogActividad::create([
                    'id_usuario' => Auth::id(),
                    'accion' => $action,
                    'tabla_afectada' => 'NOTA_FINAL',
                    'id_registro_afectado' => $notaFinal->id_nota_final,
                    'datos_anteriores' => $oldData,
                    'datos_nuevos' => json_encode($notaFinal->toArray()),
                    'fecha_hora' => Carbon::now(),
                    'direccion_ip' => $request->ip(),
                    'modulo' => 'Académico',
                ]);
                // Check if all enrolled students in the group have a final grade
                $totalEnrolled = DetalleInscripcion::where('id_grupo', $detalle->id_grupo)->count();
                $totalGrades = NotaFinal::whereHas('detalleInscripcion', function($q) use ($detalle) {
                    $q->where('id_grupo', $detalle->id_grupo);
                })->count();

                if ($totalEnrolled > 0 && $totalEnrolled === $totalGrades) {
                    $detalle->grupo->estado = 'Finalizado';
                    $detalle->grupo->save();
                }
            });

            return redirect()->route('notas.index')->with('success', 'Nota del estudiante registrada correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al guardar la nota: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified grade from storage.
     */
    public function destroy($id)
    {
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'No autorizado.');
        }

        try {
            DB::transaction(function () use ($id) {
                $nota = NotaFinal::findOrFail($id);
                $nota->delete();

                LogActividad::create([
                    'id_usuario' => Auth::id(),
                    'accion' => 'DELETE',
                    'tabla_afectada' => 'NOTA_FINAL',
                    'id_registro_afectado' => $id,
                    'fecha_hora' => Carbon::now(),
                    'direccion_ip' => request()->ip(),
                    'modulo' => 'Académico',
                ]);
            });

            return redirect()->route('notas.index')->with('success', 'Nota eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar la nota: ' . $e->getMessage()]);
        }
    }
}
