<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConceptoPago;
use Illuminate\Http\Request;

class TarifaController extends Controller
{
    public function index()
    {
        $tarifas = ConceptoPago::all();
        return view('admin.tarifas.index', compact('tarifas'));
    }

    public function create()
    {
        return view('admin.tarifas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|unique:CONCEPTO_PAGO,codigo|max:20',
            'nombre' => 'required|string|max:100',
            'monto_base' => 'required|numeric|min:0',
            'tipo' => 'required|in:Unico,Mensual,Otro',
            'es_obligatorio' => 'required|boolean',
            'estado' => 'required|in:Activo,Inactivo'
        ]);

        ConceptoPago::create($request->all());

        return redirect()->route('admin.tarifas.index')->with('success', 'Tarifa creada con éxito.');
    }

    public function edit($id)
    {
        $tarifa = ConceptoPago::findOrFail($id);
        return view('admin.tarifas.edit', compact('tarifa'));
    }

    public function update(Request $request, $id)
    {
        $tarifa = ConceptoPago::findOrFail($id);
        
        $request->validate([
            'codigo' => 'required|string|max:20|unique:CONCEPTO_PAGO,codigo,' . $tarifa->id_concepto . ',id_concepto',
            'nombre' => 'required|string|max:100',
            'monto_base' => 'required|numeric|min:0',
            'tipo' => 'required|in:Unico,Mensual,Otro',
            'es_obligatorio' => 'required|boolean',
            'estado' => 'required|in:Activo,Inactivo'
        ]);

        $tarifa->update($request->all());

        return redirect()->route('admin.tarifas.index')->with('success', 'Tarifa actualizada con éxito.');
    }

    public function destroy($id)
    {
        $tarifa = ConceptoPago::findOrFail($id);
        $tarifa->delete();
        return redirect()->route('admin.tarifas.index')->with('success', 'Tarifa eliminada con éxito.');
    }
}
