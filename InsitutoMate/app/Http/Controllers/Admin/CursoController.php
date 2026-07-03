<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Especialidad;
use App\Models\Nivel;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    public function index()
    {
        $cursos = Curso::with(['especialidad', 'nivel'])->get();
        return view('admin.cursos.index', compact('cursos'));
    }

    public function create()
    {
        $especialidades = Especialidad::where('estado', 'Activa')->get();
        $niveles = Nivel::all();
        return view('admin.cursos.create', compact('especialidades', 'niveles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_especialidad' => 'required|exists:ESPECIALIDAD,id_especialidad',
            'id_nivel' => 'required|exists:NIVEL,id_nivel',
            'codigo_curso' => 'required|string|unique:CURSO,codigo_curso|max:20',
            'nombre_curso' => 'required|string|max:100',
            'creditos' => 'required|integer|min:1',
            'duracion_horas' => 'nullable|integer|min:1',
            'estado' => 'required|in:Activo,Inactivo'
        ]);

        Curso::create($request->all());

        return redirect()->route('admin.cursos.index')->with('success', 'Curso creado con éxito.');
    }

    public function edit(Curso $curso)
    {
        $especialidades = Especialidad::where('estado', 'Activa')->get();
        $niveles = Nivel::all();
        return view('admin.cursos.edit', compact('curso', 'especialidades', 'niveles'));
    }

    public function update(Request $request, Curso $curso)
    {
        $request->validate([
            'id_especialidad' => 'required|exists:ESPECIALIDAD,id_especialidad',
            'id_nivel' => 'required|exists:NIVEL,id_nivel',
            'codigo_curso' => 'required|string|max:20|unique:CURSO,codigo_curso,' . $curso->id_curso . ',id_curso',
            'nombre_curso' => 'required|string|max:100',
            'creditos' => 'required|integer|min:1',
            'duracion_horas' => 'nullable|integer|min:1',
            'estado' => 'required|in:Activo,Inactivo'
        ]);

        $curso->update($request->all());

        return redirect()->route('admin.cursos.index')->with('success', 'Curso actualizado con éxito.');
    }

    public function destroy(Curso $curso)
    {
        $curso->delete();
        return redirect()->route('admin.cursos.index')->with('success', 'Curso eliminado con éxito.');
    }
}
