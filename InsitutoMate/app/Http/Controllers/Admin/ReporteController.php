<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Estudiante;
use App\Models\Pago;
use App\Models\Matricula;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function index()
    {
        return view('admin.reportes.index');
    }

    public function estudiantes(Request $request)
    {
        $query = \App\Models\DetalleInscripcion::with([
            'inscripcion.matricula.estudiante.persona',
            'grupo.curso',
            'notaFinal'
        ])->whereHas('inscripcion.matricula.estudiante', function ($q) use ($request) {
            if ($request->has('estado') && $request->estado != '') {
                $q->where('estado', $request->estado);
            }
        });

        if ($request->has('ci') && $request->ci != '') {
            $query->whereHas('inscripcion.matricula.estudiante.persona', function($q) use ($request) {
                $q->where('numero_documento', 'like', '%' . $request->ci . '%');
            });
        }

        $detalles = $query->get();

        return view('admin.reportes.estudiantes', compact('detalles'));
    }

    public function financiero(Request $request)
    {
        $query = Pago::with(['estudiante.persona', 'usuario'])->where('estado', 'Registrado');

        if ($request->has('fecha_inicio') && $request->fecha_inicio != '') {
            $query->whereDate('fecha_pago', '>=', $request->fecha_inicio);
        }
        if ($request->has('fecha_fin') && $request->fecha_fin != '') {
            $query->whereDate('fecha_pago', '<=', $request->fecha_fin);
        }

        $pagos = $query->orderBy('fecha_pago', 'desc')->get();
        $totalRecaudado = $pagos->sum('monto_total');

        return view('admin.reportes.financiero', compact('pagos', 'totalRecaudado'));
    }
}
