<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Models\Curso;
use App\Models\Docente;
use App\Models\Aula;
use App\Models\PeriodoAcademico;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    public function index()
    {
        $grupos = Grupo::with(['curso.especialidad', 'docente.persona', 'aula', 'periodo', 'horarios'])->get();
        $cursos = Curso::where('estado', 'Activo')->get();
        $docentes = Docente::with('persona')->where('estado', 'Activo')->get();
        $aulas = Aula::all();
        $periodos = PeriodoAcademico::orderBy('fecha_inicio', 'desc')->get();

        return view('admin.grupos.index', compact('grupos', 'cursos', 'docentes', 'aulas', 'periodos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_curso' => 'required|exists:CURSO,id_curso',
            'id_docente' => 'required|exists:DOCENTE,id_docente',
            'id_aula' => 'required|exists:AULA,id_aula',
            'id_periodo' => 'required|exists:PERIODO_ACADEMICO,id_periodo',
            'numero_grupo' => 'required|integer|min:1',
            'cupo_maximo' => 'required|integer|min:1|max:100',
            'estado' => 'required|in:Abierto,Cerrado,Finalizado'
        ]);

        $grupo = Grupo::create($request->except('turno'));

        if ($request->has('turno')) {
            $turno = $request->turno;
            $horas = $this->getHorasPorTurno($turno);
            if ($horas) {
                $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
                foreach ($dias as $dia) {
                    $grupo->horarios()->create([
                        'dia_semana' => $dia,
                        'hora_inicio' => $horas['inicio'],
                        'hora_fin' => $horas['fin']
                    ]);
                }
            }
        }

        return redirect()->route('admin.grupos.index')->with('success', 'Grupo creado con éxito.');
    }

    public function update(Request $request, Grupo $grupo)
    {
        $request->validate([
            'id_curso' => 'required|exists:CURSO,id_curso',
            'id_docente' => 'required|exists:DOCENTE,id_docente',
            'id_aula' => 'required|exists:AULA,id_aula',
            'id_periodo' => 'required|exists:PERIODO_ACADEMICO,id_periodo',
            'numero_grupo' => 'required|integer|min:1',
            'cupo_maximo' => 'required|integer|min:1|max:100',
            'estado' => 'required|in:Abierto,Cerrado,Finalizado'
        ]);

        $grupo->update($request->except('turno'));

        if ($request->has('turno')) {
            $turno = $request->turno;
            $horas = $this->getHorasPorTurno($turno);
            if ($horas) {
                $grupo->horarios()->delete(); // Limpiar horarios anteriores
                $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
                foreach ($dias as $dia) {
                    $grupo->horarios()->create([
                        'dia_semana' => $dia,
                        'hora_inicio' => $horas['inicio'],
                        'hora_fin' => $horas['fin']
                    ]);
                }
            }
        }

        return redirect()->route('admin.grupos.index')->with('success', 'Grupo actualizado con éxito.');
    }

    public function destroy(Grupo $grupo)
    {
        // En un caso real se debe verificar si no tiene estudiantes inscritos antes de eliminar
        try {
            $grupo->delete();
            return redirect()->route('admin.grupos.index')->with('success', 'Grupo eliminado con éxito.');
        } catch (\Exception $e) {
            return redirect()->route('admin.grupos.index')->with('error', 'No se puede eliminar el grupo porque tiene registros asociados.');
        }
    }

    private function getHorasPorTurno($turno)
    {
        switch ($turno) {
            case 'Mañana':
                return ['inicio' => '08:30:00', 'fin' => '10:30:00'];
            case 'Medio Día':
                return ['inicio' => '11:30:00', 'fin' => '13:30:00'];
            case 'Tarde':
                return ['inicio' => '14:30:00', 'fin' => '16:30:00'];
            case 'Noche':
                return ['inicio' => '19:30:00', 'fin' => '21:30:00'];
            default:
                return null;
        }
    }
}
