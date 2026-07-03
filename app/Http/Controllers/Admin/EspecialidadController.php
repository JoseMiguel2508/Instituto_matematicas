<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Especialidad;
use Illuminate\Http\Request;

class EspecialidadController extends Controller
{
    public function index()
    {
        $especialidades = Especialidad::all();
        return view('admin.especialidades.index', compact('especialidades'));
    }

    public function create()
    {
        return view('admin.especialidades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|unique:ESPECIALIDAD,codigo|max:20',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:Activa,Inactiva'
        ]);

        Especialidad::create($request->all());

        return redirect()->route('admin.especialidades.index')->with('success', 'Especialidad creada con éxito.');
    }

    public function edit(Especialidad $especialidad)
    {
        return view('admin.especialidades.edit', compact('especialidad'));
    }

    public function update(Request $request, Especialidad $especialidad)
    {
        $request->validate([
            'codigo' => 'required|string|max:20|unique:ESPECIALIDAD,codigo,' . $especialidad->id_especialidad . ',id_especialidad',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:Activa,Inactiva'
        ]);

        $especialidad->update($request->all());

        return redirect()->route('admin.especialidades.index')->with('success', 'Especialidad actualizada con éxito.');
    }

    public function destroy(Especialidad $especialidad)
    {
        $especialidad->delete();
        return redirect()->route('admin.especialidades.index')->with('success', 'Especialidad eliminada con éxito.');
    }
}
