<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Empleado;
use App\Models\User;
use App\Models\Rol;
use App\Models\Cargo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados = Empleado::with(['persona', 'cargo'])->get();
        $cargos = Cargo::all();
        $roles = Rol::all();
        return view('admin.empleados.index', compact('empleados', 'cargos', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo_documento' => 'required|string|max:20',
            'numero_documento' => 'required|string|max:20|unique:PERSONA,numero_documento',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'codigo_empleado' => 'required|string|max:20|unique:EMPLEADO,codigo_empleado',
            'id_cargo' => 'required|exists:CARGO,id_cargo',
            'crear_usuario' => 'nullable',
            'id_rol' => 'nullable|required_with:crear_usuario|exists:ROL,id_rol',
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

                // Crear Empleado
                Empleado::create([
                    'id_empleado' => $persona->id_persona,
                    'codigo_empleado' => $request->codigo_empleado,
                    'id_cargo' => $request->id_cargo,
                    'fecha_contratacion' => $request->fecha_contratacion,
                    'tipo_contrato' => $request->tipo_contrato,
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

                    $user->roles()->attach($request->id_rol);
                }
            });

            return redirect()->route('admin.empleados.index')->with('success', 'Empleado registrado exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar empleado: ' . $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $empleado = Empleado::with('persona')->findOrFail($id);

        $request->validate([
            'tipo_documento' => 'required|string|max:20',
            'numero_documento' => 'required|string|max:20|unique:PERSONA,numero_documento,' . $empleado->id_empleado . ',id_persona',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'codigo_empleado' => 'required|string|max:20|unique:EMPLEADO,codigo_empleado,' . $empleado->id_empleado . ',id_empleado',
            'id_cargo' => 'required|exists:CARGO,id_cargo',
            'tipo_contrato' => 'nullable|string|max:50',
            'fecha_contratacion' => 'nullable|date',
            'estado' => 'required|string|in:Activo,Inactivo,Suspendido',
        ]);

        try {
            DB::transaction(function () use ($request, $empleado) {
                $persona = $empleado->persona;
                
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

                $empleado->update([
                    'codigo_empleado' => $request->codigo_empleado,
                    'id_cargo' => $request->id_cargo,
                    'tipo_contrato' => $request->tipo_contrato,
                    'fecha_contratacion' => $request->fecha_contratacion,
                    'estado' => $request->estado,
                ]);
            });

            return redirect()->route('admin.empleados.index')->with('success', 'Empleado actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar empleado: ' . $e->getMessage()])->withInput();
        }
    }
}
