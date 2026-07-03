<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Docente;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DocenteController extends Controller
{
    public function index()
    {
        $docentes = Docente::with('persona')->get();
        // Obtener solo el rol de Docente si existe
        $rolDocente = Rol::where('nombre', 'Docente')->first();
        return view('admin.docentes.index', compact('docentes', 'rolDocente'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo_documento' => 'required|string|max:20',
            'numero_documento' => 'required|string|max:20|unique:PERSONA,numero_documento',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'codigo_docente' => 'required|string|max:20|unique:DOCENTE,codigo_docente',
            'grado_academico' => 'nullable|string|max:50',
            'crear_usuario' => 'nullable',
            'username' => 'nullable|required_with:crear_usuario|string|max:50|unique:USUARIO,username',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Crear Persona
                $persona = Persona::create([
                    'tipo_documento' => $request->tipo_documento,
                    'numero_documento' => $request->numero_documento,
                    'nombres' => $request->nombres,
                    'apellidos' => $request->apellidos,
                    'fecha_nacimiento' => $request->fecha_nacimiento,
                    'direccion' => $request->direccion,
                    'telefono' => $request->telefono,
                    'email' => $request->email,
                ]);

                // Crear Docente
                Docente::create([
                    'id_docente' => $persona->id_persona,
                    'codigo_docente' => $request->codigo_docente,
                    'grado_academico' => $request->grado_academico,
                    'fecha_contratacion' => $request->fecha_contratacion,
                    'estado' => 'Activo',
                ]);

                // Crear Usuario y Rol
                if ($request->has('crear_usuario')) {
                    $user = User::create([
                        'id_persona' => $persona->id_persona,
                        'username' => $request->username,
                        'password_hash' => Hash::make($request->numero_documento),
                        'estado' => 'Activo',
                    ]);

                    $rolDocente = Rol::where('nombre', 'Docente')->first();
                    if ($rolDocente) {
                        $user->roles()->attach($rolDocente->id_rol);
                    }
                }
            });

            return redirect()->route('admin.docentes.index')->with('success', 'Docente registrado exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar docente: ' . $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $docente = Docente::with('persona')->findOrFail($id);

        $request->validate([
            'tipo_documento' => 'required|string|max:20',
            'numero_documento' => 'required|string|max:20|unique:PERSONA,numero_documento,' . $docente->id_docente . ',id_persona',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'codigo_docente' => 'required|string|max:20|unique:DOCENTE,codigo_docente,' . $docente->id_docente . ',id_docente',
            'grado_academico' => 'nullable|string|max:50',
            'fecha_contratacion' => 'nullable|date',
            'estado' => 'required|string|in:Activo,Inactivo,Suspendido',
        ]);

        try {
            DB::transaction(function () use ($request, $docente) {
                $persona = $docente->persona;
                
                $persona->update([
                    'tipo_documento' => $request->tipo_documento,
                    'numero_documento' => $request->numero_documento,
                    'nombres' => $request->nombres,
                    'apellidos' => $request->apellidos,
                    'fecha_nacimiento' => $request->fecha_nacimiento,
                    'direccion' => $request->direccion,
                    'telefono' => $request->telefono,
                    'email' => $request->email,
                ]);

                $docente->update([
                    'codigo_docente' => $request->codigo_docente,
                    'grado_academico' => $request->grado_academico,
                    'fecha_contratacion' => $request->fecha_contratacion,
                    'estado' => $request->estado,
                ]);
            });

            return redirect()->route('admin.docentes.index')->with('success', 'Docente actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar docente: ' . $e->getMessage()])->withInput();
        }
    }
}
