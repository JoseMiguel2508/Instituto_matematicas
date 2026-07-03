<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\DetallePago;
use App\Models\ConceptoPago;
use App\Models\Estudiante;
use App\Models\LogActividad;
use App\Models\DeudaEstudiante;
use App\Models\SesionCaja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PagoController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index()
    {
        $pagos = Pago::with(['estudiante.persona', 'usuario.persona'])->get();
        $conceptos = ConceptoPago::where('estado', 'Activo')->get();
        $estudiantes = Estudiante::with('persona')->where('estado', 'Activo')->get();

        return view('pagos.index', compact('pagos', 'conceptos', 'estudiantes'));
    }

    /**
     * Get pending debts for a student via AJAX.
     */
    public function getDeudas($id_estudiante)
    {
        $deudas = DeudaEstudiante::with(['concepto', 'periodo'])
            ->where('id_estudiante', $id_estudiante)
            ->where('estado', 'Pendiente')
            ->get();

        return response()->json($deudas);
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_estudiante' => 'required|integer|exists:ESTUDIANTE,id_estudiante',
            'deudas' => 'required|array|min:1',
            'deudas.*' => 'exists:DEUDA_ESTUDIANTE,id_deuda',
            'tipo_comprobante' => 'required|string|in:Boleta,Factura,Ticket',
            'metodo_pago' => 'required|string|in:Efectivo,QR,Transferencia Bancaria',
            'monto_total' => 'required|numeric|min:0.01',
            'observaciones' => 'nullable|string|max:300',
        ]);

        try {
            // Verificar si el usuario tiene una sesión de caja abierta
            $sesionAbierta = SesionCaja::where('id_usuario_apertura', Auth::id())
                                       ->where('estado', 'Abierta')
                                       ->first();

            if (!$sesionAbierta) {
                return back()->withErrors(['error' => 'No puedes registrar cobros. Debes aperturar caja primero.']);
            }

            $pago = DB::transaction(function () use ($request, $sesionAbierta) {
                $estudiante = Estudiante::findOrFail($request->id_estudiante);
                $activeMatricula = $estudiante->matriculas()->where('estado', 'Activa')->first();
                $idMatricula = $activeMatricula ? $activeMatricula->id_matricula : null;

                // Validar deudas y calcular monto real antes de crear el pago
                $deudas = DeudaEstudiante::whereIn('id_deuda', $request->deudas)
                    ->where('id_estudiante', $request->id_estudiante)
                    ->where('estado', 'Pendiente')
                    ->get();
                
                if ($deudas->count() !== count(array_unique($request->deudas))) {
                    throw new \Exception('Algunas deudas no existen, ya están pagadas o no pertenecen a este estudiante.');
                }

                $montoReal = (float) $deudas->sum('monto');
                
                if (abs($montoReal - (float) $request->monto_total) > 0.01) {
                    throw new \Exception('El monto enviado no coincide con el monto real de las deudas (S/ ' . number_format($montoReal, 2) . ').');
                }

                // Generate comprobante number
                $lastPagoId = Pago::max('id_pago') ?? 0;
                $invoiceNum = 'B001-' . str_pad($lastPagoId + 1, 6, '0', STR_PAD_LEFT);

                // 1. Create Pago
                $pago = Pago::create([
                    'id_estudiante' => $request->id_estudiante,
                    'id_matricula' => $idMatricula,
                    'id_usuario_registra' => Auth::id(),
                    'id_sesion_caja' => $sesionAbierta->id_sesion_caja,
                    'numero_comprobante' => $invoiceNum,
                    'tipo_comprobante' => $request->tipo_comprobante,
                    'monto_total' => $montoReal,
                    'fecha_pago' => Carbon::now(),
                    'metodo_pago' => $request->metodo_pago,
                    'estado' => 'Registrado',
                    'observaciones' => $request->observaciones,
                ]);

                // 2. Process each Deuda
                foreach ($deudas as $deuda) {
                    // Update Deuda status
                    $deuda->estado = 'Pagado';
                    $deuda->save();

                    // Create DetallePago linked to Deuda
                    DetallePago::create([
                        'id_pago' => $pago->id_pago,
                        'id_concepto' => $deuda->id_concepto,
                        'id_deuda' => $deuda->id_deuda,
                        'monto_aplicado' => $deuda->monto,
                        'descripcion' => 'Pago de ' . $deuda->concepto->nombre . ' (' . $deuda->periodo->codigo . ')',
                    ]);
                }

                // 3. Log Activity
                LogActividad::create([
                    'id_usuario' => Auth::id(),
                    'accion' => 'INSERT',
                    'tabla_afectada' => 'PAGO',
                    'id_registro_afectado' => $pago->id_pago,
                    'datos_anteriores' => null,
                    'datos_nuevos' => json_encode($pago->toArray()),
                    'fecha_hora' => Carbon::now(),
                    'direccion_ip' => $request->ip(),
                    'modulo' => 'Financiero',
                ]);

                return $pago;
            });

            return redirect()->route('pagos.index')->with('success', 'Pago registrado con éxito. Comprobante ' . $pago->numero_comprobante);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar el pago: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display printable invoice receipt.
     */
    public function showReceipt($id)
    {
        $pago = Pago::with([
            'estudiante.persona',
            'detalles.concepto',
            'usuario.persona',
            'matricula.periodo',
            'matricula.especialidad'
        ])->findOrFail($id);

        return view('pagos.receipt', compact('pago'));
    }

    /**
     * Remove the specified payment from storage.
     */
    public function destroy($id)
    {
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'No autorizado.');
        }

        try {
            DB::transaction(function () use ($id) {
                $pago = Pago::with('detalles')->findOrFail($id);
                
                // Revert debts to pending
                foreach ($pago->detalles as $detalle) {
                    if ($detalle->id_deuda) {
                        $deuda = DeudaEstudiante::find($detalle->id_deuda);
                        if ($deuda) {
                            $deuda->estado = 'Pendiente';
                            $deuda->save();
                        }
                    }
                }

                $pago->delete();

                LogActividad::create([
                    'id_usuario' => Auth::id(),
                    'accion' => 'DELETE',
                    'tabla_afectada' => 'PAGO',
                    'id_registro_afectado' => $id,
                    'fecha_hora' => Carbon::now(),
                    'direccion_ip' => request()->ip(),
                    'modulo' => 'Financiero',
                ]);
            });

            return redirect()->route('pagos.index')->with('success', 'Pago eliminado y deudas restauradas a pendiente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar el pago: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $pago = Pago::findOrFail($id);

        $request->validate([
            'tipo_comprobante' => 'required|string|in:Boleta,Factura,Ticket',
            'numero_comprobante' => 'required|string|max:20',
            'metodo_pago' => 'required|string|in:Efectivo,QR,Transferencia Bancaria',
            'observaciones' => 'nullable|string|max:300',
        ]);

        try {
            DB::transaction(function () use ($request, $pago) {
                $oldData = $pago->toArray();
                
                $pago->update([
                    'tipo_comprobante' => $request->tipo_comprobante,
                    'numero_comprobante' => $request->numero_comprobante,
                    'metodo_pago' => $request->metodo_pago,
                    'observaciones' => $request->observaciones,
                ]);

                LogActividad::create([
                    'id_usuario' => Auth::id(),
                    'accion' => 'UPDATE',
                    'tabla_afectada' => 'PAGO',
                    'id_registro_afectado' => $pago->id_pago,
                    'datos_anteriores' => json_encode($oldData),
                    'datos_nuevos' => json_encode($pago->toArray()),
                    'fecha_hora' => Carbon::now(),
                    'direccion_ip' => request()->ip(),
                    'modulo' => 'Financiero',
                ]);
            });

            return redirect()->route('pagos.index')->with('success', 'Detalles del pago actualizados correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar el pago: ' . $e->getMessage()]);
        }
    }
}
