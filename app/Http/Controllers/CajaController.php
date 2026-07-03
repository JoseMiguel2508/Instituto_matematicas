<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caja;
use App\Models\SesionCaja;
use App\Models\Pago;
use App\Models\LogActividad;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    public function index()
    {
        $usuarioId = Auth::id();
        
        // Obtener la caja activa por defecto
        $caja = Caja::where('estado', 'Activa')->first();
        
        if (!$caja) {
            return back()->withErrors(['error' => 'No hay cajas activas configuradas en el sistema.']);
        }

        // Buscar si hay una sesión abierta para la caja
        $sesionAbierta = SesionCaja::where('id_caja', $caja->id_caja)
                                   ->where('estado', 'Abierta')
                                   ->first();

        $ingresosEfectivo = 0;
        $ingresosOnline = 0;

        if ($sesionAbierta) {
            // Calcular ingresos de esta sesión
            $ingresosEfectivo = Pago::where('id_sesion_caja', $sesionAbierta->id_sesion_caja)
                                    ->where('metodo_pago', 'Efectivo')
                                    ->sum('monto_total');
                                    
            $ingresosOnline = Pago::where('id_sesion_caja', $sesionAbierta->id_sesion_caja)
                                  ->whereIn('metodo_pago', ['QR', 'Transferencia Bancaria', 'Tarjeta', 'Transferencia', 'Yape', 'Plin'])
                                  ->sum('monto_total');
        }

        // Obtener historial de sesiones del usuario
        $historial = SesionCaja::where('id_usuario_apertura', $usuarioId)
                               ->orderBy('fecha_apertura', 'desc')
                               ->take(10)
                               ->get();

        return view('caja.index', compact('caja', 'sesionAbierta', 'ingresosEfectivo', 'ingresosOnline', 'historial'));
    }

    public function abrir(Request $request)
    {
        $request->validate([
            'id_caja' => 'required|exists:CAJA,id_caja',
            'monto_inicial' => 'required|numeric|min:0'
        ]);

        $usuarioId = Auth::id();

        // Verificar si la caja ya está abierta por alguien
        $sesionExistente = SesionCaja::where('id_caja', $request->id_caja)
                                     ->where('estado', 'Abierta')
                                     ->first();

        if ($sesionExistente) {
            return back()->withErrors(['error' => 'Esta caja ya se encuentra abierta actualmente.']);
        }

        try {
            DB::transaction(function() use ($request, $usuarioId) {
                $sesion = SesionCaja::create([
                    'id_caja' => $request->id_caja,
                    'id_usuario_apertura' => $usuarioId,
                    'monto_inicial' => $request->monto_inicial,
                    'fecha_apertura' => Carbon::now(),
                    'estado' => 'Abierta',
                    'observaciones_apertura' => $request->observaciones_apertura ?? null
                ]);

                LogActividad::create([
                    'id_usuario' => $usuarioId,
                    'accion' => 'INSERT',
                    'tabla_afectada' => 'SESION_CAJA',
                    'id_registro_afectado' => $sesion->id_sesion_caja,
                    'datos_nuevos' => json_encode($sesion->toArray()),
                    'fecha_hora' => Carbon::now(),
                    'direccion_ip' => $request->ip(),
                    'modulo' => 'Caja'
                ]);
            });

            return redirect()->route('caja.index')->with('success', 'Caja aperturada exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al aperturar caja: ' . $e->getMessage()]);
        }
    }

    public function cerrar(Request $request)
    {
        $request->validate([
            'id_sesion_caja' => 'required|exists:SESION_CAJA,id_sesion_caja',
            'monto_final_real' => 'required|numeric|min:0'
        ]);

        $sesion = SesionCaja::where('id_sesion_caja', $request->id_sesion_caja)
                            ->where('estado', 'Abierta')
                            ->firstOrFail();

        $ingresosEfectivo = Pago::where('id_sesion_caja', $sesion->id_sesion_caja)
                                ->where('metodo_pago', 'Efectivo')
                                ->sum('monto_total');

        $montoEsperado = $sesion->monto_inicial + $ingresosEfectivo;
        $diferencia = $request->monto_final_real - $montoEsperado;

        try {
            DB::transaction(function() use ($request, $sesion, $montoEsperado, $diferencia) {
                $oldData = json_encode($sesion->toArray());
                
                $sesion->update([
                    'id_usuario_cierre' => Auth::id(),
                    'fecha_cierre' => Carbon::now(),
                    'monto_final_esperado' => $montoEsperado,
                    'monto_final_real' => $request->monto_final_real,
                    'diferencia' => $diferencia,
                    'estado' => 'Cerrada',
                    'observaciones_cierre' => $request->observaciones_cierre ?? null
                ]);

                LogActividad::create([
                    'id_usuario' => Auth::id(),
                    'accion' => 'UPDATE',
                    'tabla_afectada' => 'SESION_CAJA',
                    'id_registro_afectado' => $sesion->id_sesion_caja,
                    'datos_anteriores' => $oldData,
                    'datos_nuevos' => json_encode($sesion->toArray()),
                    'fecha_hora' => Carbon::now(),
                    'direccion_ip' => $request->ip(),
                    'modulo' => 'Caja'
                ]);
            });

            return redirect()->route('caja.index')->with('success', 'Caja cerrada exitosamente. Diferencia: Bs. ' . number_format($diferencia, 2));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al cerrar caja: ' . $e->getMessage()]);
        }
    }
}
