<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Estudiante;
use App\Models\Docente;
use App\Models\LogActividad;
use App\Models\PeriodoAcademico;
use App\Models\ConceptoPago;
use App\Models\DeudaEstudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EstudianteController extends Controller
{
    /**
     * Display a listing of students and teachers.
     */
    public function index(Request $request)
    {
        $query = Estudiante::with(['persona', 'matriculas']);
        
        if ($request->has('buscar_ci') && !empty($request->buscar_ci)) {
            $query->whereHas('persona', function($q) use ($request) {
                $q->where('numero_documento', 'LIKE', '%' . $request->buscar_ci . '%');
            });
        }
        
        $estudiantes = $query->paginate(15);
        
        $docentes = Docente::with('persona')->get();

        return view('estudiantes.index', compact('estudiantes', 'docentes'));
    }

    /**
     * Search student by CI (Document Number) API endpoint.
     */
    public function searchByCI(Request $request)
    {
        $ci = $request->query('ci');
        
        if (!$ci) {
            return response()->json(['error' => 'No CI provided'], 400);
        }

        $estudiante = Estudiante::with('persona')
            ->whereHas('persona', function($q) use ($ci) {
                $q->where('numero_documento', $ci);
            })
            ->where('estado', 'Activo')
            ->first();

        if ($estudiante) {
            return response()->json([
                'success' => true,
                'id_estudiante' => $estudiante->id_estudiante,
                'nombre_completo' => $estudiante->persona->nombre_completo,
                'numero_documento' => $estudiante->persona->numero_documento,
                'codigo_estudiante' => $estudiante->codigo_estudiante
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Estudiante no encontrado o inactivo.'
        ], 404);
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo_documento' => 'required|string|in:DNI,CE,Pasaporte',
            'numero_documento' => 'required|string|unique:PERSONA,numero_documento',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'direccion' => 'nullable|string|max:200',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'modalidad_estudio' => 'required|string|in:Especialidad,Curso Libre',
        ]);

        $codigoEstudiante = substr($request->numero_documento, -2);

        // Check unique code constraint
        if (Estudiante::where('codigo_estudiante', $codigoEstudiante)->exists()) {
            return back()->withErrors([
                'numero_documento' => 'Los últimos 2 dígitos del documento (' . $codigoEstudiante . ') ya están registrados como código de otro estudiante.',
            ])->withInput();
        }

        try {
            DB::statement('CALL sp_crear_estudiante(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $request->tipo_documento,
                $request->numero_documento,
                $request->nombres,
                $request->apellidos,
                $request->fecha_nacimiento,
                $request->direccion,
                $request->telefono,
                $request->email,
                $codigoEstudiante,
                $request->modalidad_estudio
            ]);

            return redirect()->route('estudiantes.index')->with('success', 'Estudiante registrado correctamente (SP).');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar el estudiante: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Update student status.
     */
    public function update(Request $request, $id)
    {
        $estudiante = Estudiante::with('persona')->findOrFail($id);

        if ($request->has('nombres')) {
            // Es edición completa
            $request->validate([
                'tipo_documento' => 'required|string|in:DNI,CE,Pasaporte',
                'numero_documento' => 'required|string|unique:PERSONA,numero_documento,' . $estudiante->id_estudiante . ',id_persona',
                'nombres' => 'required|string|max:100',
                'apellidos' => 'required|string|max:100',
                'fecha_nacimiento' => 'nullable|date',
                'direccion' => 'nullable|string|max:200',
                'telefono' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:100',
                'estado' => 'required|string|in:Activo,Egresado,Retirado',
                'modalidad_estudio' => 'required|string|in:Especialidad,Curso Libre',
            ]);

            try {
                DB::statement('CALL sp_actualizar_estudiante(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                    $id,
                    $request->tipo_documento,
                    $request->numero_documento,
                    $request->nombres,
                    $request->apellidos,
                    $request->fecha_nacimiento,
                    $request->direccion,
                    $request->telefono,
                    $request->email,
                    $request->estado,
                    $request->modalidad_estudio
                ]);

                return redirect()->route('estudiantes.index')->with('success', 'Datos del estudiante actualizados (SP).');
            } catch (\Exception $e) {
                return back()->withErrors(['error' => 'Error al actualizar el estudiante: ' . $e->getMessage()]);
            }
        } else {
            // Solo actualización de estado rápido
            $request->validate([
                'estado' => 'required|string|in:Activo,Egresado,Retirado',
            ]);

            DB::statement('CALL sp_actualizar_estudiante(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $id,
                $estudiante->persona->tipo_documento,
                $estudiante->persona->numero_documento,
                $estudiante->persona->nombres,
                $estudiante->persona->apellidos,
                $estudiante->persona->fecha_nacimiento,
                $estudiante->persona->direccion,
                $estudiante->persona->telefono,
                $estudiante->persona->email,
                $request->estado,
                $estudiante->modalidad_estudio
            ]);

            return redirect()->route('estudiantes.index')->with('success', 'Estado del estudiante actualizado (SP).');
        }
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy($id)
    {
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'No autorizado.');
        }

        try {
            DB::statement('CALL sp_eliminar_estudiante(?)', [$id]);

            return redirect()->route('estudiantes.index')->with('success', 'Estudiante eliminado correctamente (SP).');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar el estudiante: ' . $e->getMessage()]);
        }
    }
}
