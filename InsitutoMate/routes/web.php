<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\MatriculaController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\CertificadoController;

// Redirect root to dashboard/login
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (all authenticated users)
Route::middleware(['auth'])->group(function () {

    // Dashboard - accessible to all authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Endpoint global para buscar estudiantes por CI (usado por Cajeros, Secretarias, etc)
    Route::get('/estudiantes/search-ci', [EstudianteController::class, 'searchByCI'])->name('estudiantes.search');

    // ─── Módulo: Personas / Estudiantes / Docentes ───────────────────────
    // Roles: Administrador, Director, Secretaria
    Route::middleware('role:Administrador,Director,Secretaria')->group(function () {
        Route::get('/estudiantes', [EstudianteController::class, 'index'])->name('estudiantes.index');
        Route::post('/estudiantes/store', [EstudianteController::class, 'store'])->name('estudiantes.store');
        Route::post('/estudiantes/update/{id}', [EstudianteController::class, 'update'])->name('estudiantes.update');
        Route::get('/estudiantes/{id}/certificado/{especialidad}', [CertificadoController::class, 'generarCertificado'])->name('estudiantes.certificado');
    });

    // ─── Módulo: Matrículas ───────────────────────────────────────────────
    // Roles: Administrador, Director, Coordinador, Secretaria
    Route::middleware('role:Administrador,Director,Coordinador,Secretaria')->group(function () {
        Route::get('/matriculas', [MatriculaController::class, 'index'])->name('matriculas.index');
        Route::post('/matriculas/store', [MatriculaController::class, 'store'])->name('matriculas.store');
        Route::post('/matriculas/update/{id}', [MatriculaController::class, 'update'])->name('matriculas.update');
        Route::get('/matriculas/verificar-pago/{id_estudiante}/{id_periodo}', [MatriculaController::class, 'verificarPago'])->name('matriculas.verificarPago');
        Route::get('/matriculas/grupos-disponibles/{id_estudiante}/{id_periodo}/{id_especialidad}', [App\Http\Controllers\MatriculaController::class, 'obtenerGruposDisponibles'])->name('matriculas.grupos');
        Route::get('/matriculas/verificar-prerrequisito/{id_estudiante}/{id_grupo}', [MatriculaController::class, 'verificarPrerrequisito'])->name('matriculas.verificarPrerrequisito');
    });

    // ─── Módulo: Calificaciones ───────────────────────────────────────────
    // Roles: Administrador, Director, Coordinador, Docente
    Route::middleware('role:Administrador,Director,Coordinador,Docente')->group(function () {
        Route::get('/notas', [NotaController::class, 'index'])->name('notas.index');
        Route::post('/notas/store', [NotaController::class, 'store'])->name('notas.store');
    });

    // ─── Módulo: Pagos / Cobranzas ────────────────────────────────────────
    // Roles: Administrador, Director, Cajero
    Route::middleware('role:Administrador,Director,Cajero')->group(function () {
        Route::get('/caja', [CajaController::class, 'index'])->name('caja.index');
        Route::post('/caja/abrir', [CajaController::class, 'abrir'])->name('caja.abrir');
        Route::post('/caja/cerrar', [CajaController::class, 'cerrar'])->name('caja.cerrar');

        Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
        Route::post('/pagos/store', [PagoController::class, 'store'])->name('pagos.store');
        Route::post('/pagos/update/{id}', [PagoController::class, 'update'])->name('pagos.update');
        Route::get('/pagos/deudas/{id}', [PagoController::class, 'getDeudas']);
        Route::get('/pagos/imprimir/{id}', [PagoController::class, 'showReceipt'])->name('pagos.showReceipt');
    });

    // ─── Módulo: Eliminación (Solo Administrador) ─────────────────────────
    Route::middleware('role:Administrador')->group(function () {
        Route::post('/estudiantes/destroy/{id}', [EstudianteController::class, 'destroy'])->name('estudiantes.destroy');
        Route::post('/matriculas/destroy/{id}', [MatriculaController::class, 'destroy'])->name('matriculas.destroy');
        Route::post('/pagos/destroy/{id}', [PagoController::class, 'destroy'])->name('pagos.destroy');
        Route::post('/notas/destroy/{id}', [NotaController::class, 'destroy'])->name('notas.destroy');
    });

    // ─── Módulo: Administración Integral ──────────────────────────────────
    // Roles: Administrador
    Route::middleware('role:Administrador')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('especialidades', \App\Http\Controllers\Admin\EspecialidadController::class)->except(['show']);
        Route::resource('cursos', \App\Http\Controllers\Admin\CursoController::class)->except(['show']);
        Route::resource('grupos', \App\Http\Controllers\Admin\GrupoController::class)->except(['show', 'create', 'edit']);
        Route::resource('tarifas', \App\Http\Controllers\Admin\TarifaController::class)->except(['show']);
        Route::post('empleados/update/{id}', [\App\Http\Controllers\Admin\EmpleadoController::class, 'update'])->name('empleados.custom_update');
        Route::resource('empleados', \App\Http\Controllers\Admin\EmpleadoController::class)->except(['show']);
        
        Route::post('docentes/update/{id}', [\App\Http\Controllers\Admin\DocenteController::class, 'update'])->name('docentes.custom_update');
        Route::resource('docentes', \App\Http\Controllers\Admin\DocenteController::class)->except(['show']);
        
        // Módulo de Reportes
        Route::prefix('reportes')->name('reportes.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ReporteController::class, 'index'])->name('index');
            Route::get('/estudiantes', [\App\Http\Controllers\Admin\ReporteController::class, 'estudiantes'])->name('estudiantes');
            Route::get('/financiero', [\App\Http\Controllers\Admin\ReporteController::class, 'financiero'])->name('financiero');
        });
    });
});
