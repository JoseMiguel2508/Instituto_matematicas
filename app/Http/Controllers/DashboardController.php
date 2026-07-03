<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Docente;
use App\Models\Curso;
use App\Models\Pago;
use App\Models\LogActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard, personalized by user role.
     */
    public function index(Request $request)
    {
        Carbon::setLocale('es');

        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        // ─────────────────────────────────────────────────────────────────
        // DOCENTE DASHBOARD
        // ─────────────────────────────────────────────────────────────────
        if ($user->hasRole('Docente') && !$user->hasAnyRole(['Administrador', 'Director'])) {
            $idPersona = $user->id_persona;

            // Get groups assigned to this docente
            $misGrupos = \App\Models\Grupo::with(['curso', 'horarios', 'aula'])
                ->where('id_docente', $idPersona)
                ->where('estado', 'Abierto')
                ->get();

            $gruposCount = $misGrupos->count();

            // Count unique students across these groups
            $studentsCount = \App\Models\DetalleInscripcion::whereIn('id_grupo', $misGrupos->pluck('id_grupo'))
                ->where('estado', 'Inscrito')
                ->distinct('id_inscripcion')
                ->count('id_inscripcion');

            // Build schedules collection
            $misHorarios = collect();
            foreach ($misGrupos as $grupo) {
                foreach ($grupo->horarios as $horario) {
                    $misHorarios->push([
                        'curso'       => $grupo->curso->nombre_curso,
                        'grupo'       => $grupo->numero_grupo,
                        'aula'        => $grupo->aula ? $grupo->aula->codigo_aula : 'N/A',
                        'dia'         => $horario->dia_semana,
                        'hora_inicio' => substr($horario->hora_inicio, 0, 5),
                        'hora_fin'    => substr($horario->hora_fin, 0, 5),
                    ]);
                }
            }
            $misHorarios = $misHorarios->sortBy('hora_inicio');

            // Students per group (with grades)
            $alumnosPorGrupo = [];
            foreach ($misGrupos as $grupo) {
                $detalles = \App\Models\DetalleInscripcion::with([
                        'inscripcion.matricula.estudiante.persona',
                        'notaFinal'
                    ])
                    ->where('id_grupo', $grupo->id_grupo)
                    ->where('estado', 'Inscrito')
                    ->get();

                $alumnos = $detalles->map(function ($d) {
                    $estudiante = optional(optional(optional($d->inscripcion)->matricula)->estudiante);
                    $persona    = optional($estudiante->persona);
                    $nota       = $d->notaFinal;
                    return [
                        'nombre'     => $persona->nombre_completo ?? 'N/A',
                        'codigo'     => $estudiante->codigo_estudiante ?? 'N/A',
                        'nota'       => $nota ? number_format($nota->nota, 2) : null,
                        'nota_estado'=> $nota ? $nota->estado : null,
                    ];
                })->filter(fn($a) => $a['nombre'] !== 'N/A')->values();

                $alumnosPorGrupo[$grupo->id_grupo] = [
                    'grupo'   => $grupo,
                    'alumnos' => $alumnos,
                ];
            }

            // Alerta: Estudiantes sin nota final en grupos abiertos
            $faltanNotas = false;
            foreach ($alumnosPorGrupo as $grupoData) {
                foreach ($grupoData['alumnos'] as $alumno) {
                    if (is_null($alumno['nota'])) {
                        $faltanNotas = true;
                        break 2;
                    }
                }
            }

            return view('dashboard', compact(
                'gruposCount',
                'studentsCount',
                'misGrupos',
                'misHorarios',
                'alumnosPorGrupo',
                'faltanNotas'
            ));
        }

        // ─────────────────────────────────────────────────────────────────
        // CAJERO DASHBOARD
        // ─────────────────────────────────────────────────────────────────
        if ($user->hasRole('Cajero') && !$user->hasAnyRole(['Administrador', 'Director'])) {

            // Comprobar si tiene caja abierta
            $sesionCajaActiva = \App\Models\SesionCaja::where('id_usuario_apertura', $user->id_usuario)
                ->where('estado', 'Abierta')
                ->first();

            // KPIs de caja: hoy (Solo del usuario actual)
            $pagosDia = Pago::where('estado', 'Registrado')
                ->where('id_usuario_registra', $user->id_usuario)
                ->whereDate('fecha_pago', Carbon::today())
                ->count();
            $montoDia = Pago::where('estado', 'Registrado')
                ->where('id_usuario_registra', $user->id_usuario)
                ->whereDate('fecha_pago', Carbon::today())
                ->sum('monto_total');

            // KPIs de caja: mes actual (Solo del usuario actual)
            $pagosMes = Pago::where('estado', 'Registrado')
                ->where('id_usuario_registra', $user->id_usuario)
                ->whereYear('fecha_pago', Carbon::now()->year)
                ->whereMonth('fecha_pago', Carbon::now()->month)
                ->count();
            $montoMes = Pago::where('estado', 'Registrado')
                ->where('id_usuario_registra', $user->id_usuario)
                ->whereYear('fecha_pago', Carbon::now()->year)
                ->whereMonth('fecha_pago', Carbon::now()->month)
                ->sum('monto_total');

            // Pagos recientes (Solo del usuario actual)
            $recentPayments = Pago::with('estudiante.persona')
                ->where('id_usuario_registra', $user->id_usuario)
                ->orderBy('fecha_pago', 'desc')
                ->take(8)
                ->get();

            // Deudas pendientes (últimas 15 para mostrar)
            $deudasPendientes = \App\Models\DeudaEstudiante::with(['estudiante.persona', 'concepto', 'periodo'])
                ->where('estado', 'Pendiente')
                ->orderBy('fecha_generacion', 'desc')
                ->take(15)
                ->get();

            // Deudas pagadas (últimas 15 para mostrar)
            $deudasPagadas = \App\Models\DeudaEstudiante::with(['estudiante.persona', 'concepto', 'periodo'])
                ->where('estado', 'Pagado')
                ->orderBy('fecha_generacion', 'desc')
                ->take(15)
                ->get();

            // Totales de deudas
            $totalDeudaPendiente = \App\Models\DeudaEstudiante::where('estado', 'Pendiente')->sum('monto');
            $totalDeudaPagada    = \App\Models\DeudaEstudiante::where('estado', 'Pagado')->sum('monto');
            $countPendientes     = \App\Models\DeudaEstudiante::where('estado', 'Pendiente')->count();
            $countPagadas        = \App\Models\DeudaEstudiante::where('estado', 'Pagado')->count();

            return view('dashboard', compact(
                'sesionCajaActiva',
                'pagosDia', 'montoDia',
                'pagosMes', 'montoMes',
                'recentPayments',
                'deudasPendientes', 'deudasPagadas',
                'totalDeudaPendiente', 'totalDeudaPagada',
                'countPendientes', 'countPagadas'
            ));
        }

        // ─────────────────────────────────────────────────────────────────
        // ADMIN / STAFF DASHBOARD (Administrador, Director, Secretaria, etc.)
        // ─────────────────────────────────────────────────────────────────

        $studentsCount = Estudiante::where('estado', 'Activo')->count();
        $teachersCount = Docente::where('estado', 'Activo')->count();
        $coursesCount  = Curso::where('estado', 'Activo')->count();
        $totalRevenue  = Pago::where('estado', 'Registrado')->sum('monto_total');

        // Morosidad
        $studentsWithDebt = \App\Models\DeudaEstudiante::where('estado', 'Pendiente')
            ->distinct('id_estudiante')
            ->count('id_estudiante');
        $morosidadPorcentaje = $studentsCount > 0 ? round(($studentsWithDebt / $studentsCount) * 100, 1) : 0;

        $logs = [];
        if (Auth::user()->hasRole('Administrador')) {
            $logs = LogActividad::with('usuario.persona')
                ->orderBy('fecha_hora', 'desc')
                ->take(5)
                ->get();
        }

        $recentPayments = Pago::with('estudiante.persona')
            ->orderBy('fecha_pago', 'desc')
            ->take(10)
            ->get();

        $filter = $request->query('filter', '6days');
        
        if ($filter === 'mes') {
            // Último mes agrupado en 5 bloques de 6 días o por semana
            $chartData = DB::table('PAGO')
                ->select(DB::raw('DATE(fecha_pago) as date'), DB::raw('SUM(monto_total) as total'))
                ->where('estado', 'Registrado')
                ->where('fecha_pago', '>=', Carbon::now()->subDays(30))
                ->groupBy('date')
                ->get();

            $revenueData = [];
            // Dividir en 6 bloques de 5 días
            for ($i = 5; $i >= 0; $i--) {
                $start = Carbon::now()->subDays($i * 5 + 4)->startOfDay();
                $end = Carbon::now()->subDays($i * 5)->endOfDay();
                $label = $start->format('d/m') . '-' . $end->format('d/m');
                $val = 0;
                foreach ($chartData as $c) {
                    $cDate = Carbon::parse($c->date);
                    if ($cDate->between($start, $end)) {
                        $val += (float)$c->total;
                    }
                }
                $revenueData[] = ['label' => $label, 'value' => $val];
            }
        } else {
            $chartData = DB::table('PAGO')
                ->select(DB::raw('DATE(fecha_pago) as date'), DB::raw('SUM(monto_total) as total'))
                ->where('estado', 'Registrado')
                ->where('fecha_pago', '>=', Carbon::now()->subDays(5))
                ->groupBy('date')
                ->get();

            $revenueData = [];
            for ($i = 5; $i >= 0; $i--) {
                $day = Carbon::now()->subDays($i)->format('Y-m-d');
                $label = ucfirst(Carbon::now()->subDays($i)->isoFormat('dddd'));
                $val = 0;
                foreach ($chartData as $c) {
                    if ($c->date === $day) {
                        $val = (float)$c->total;
                        break;
                    }
                }
                $revenueData[] = ['label' => $label, 'value' => $val];
            }
        }

        $maxRevenue = max(array_column($revenueData, 'value'));
        if ($maxRevenue <= 0) {
            $maxRevenue = 100;
        }

        // Deudas para administrador / director
        $deudasPendientes = \App\Models\DeudaEstudiante::with(['estudiante.persona', 'concepto', 'periodo'])
            ->where('estado', 'Pendiente')
            ->orderBy('fecha_generacion', 'desc')
            ->take(15)
            ->get();

        $deudasPagadas = \App\Models\DeudaEstudiante::with(['estudiante.persona', 'concepto', 'periodo'])
            ->where('estado', 'Pagado')
            ->orderBy('fecha_generacion', 'desc')
            ->take(15)
            ->get();

        $totalDeudaPendiente = \App\Models\DeudaEstudiante::where('estado', 'Pendiente')->sum('monto');
        $totalDeudaPagada    = \App\Models\DeudaEstudiante::where('estado', 'Pagado')->sum('monto');
        $countPendientes     = \App\Models\DeudaEstudiante::where('estado', 'Pendiente')->count();
        $countPagadas        = \App\Models\DeudaEstudiante::where('estado', 'Pagado')->count();

        // Cajas para Administrador y Secretaria
        $cajas = [];
        if (Auth::user()->hasAnyRole(['Administrador', 'Secretaria', 'Director'])) {
            $cajas = \App\Models\SesionCaja::with('usuarioApertura.persona')
                ->orderBy('estado', 'asc') // 'Abierta' comes before 'Cerrada' usually, or order by date
                ->orderBy('fecha_apertura', 'desc')
                ->take(5)
                ->get();
        }

        return view('dashboard', compact(
            'studentsCount',
            'teachersCount',
            'coursesCount',
            'totalRevenue',
            'morosidadPorcentaje',
            'studentsWithDebt',
            'logs',
            'recentPayments',
            'revenueData',
            'maxRevenue',
            'deudasPendientes',
            'deudasPagadas',
            'totalDeudaPendiente',
            'totalDeudaPagada',
            'countPendientes',
            'countPagadas',
            'cajas'
        ));
    }
}
