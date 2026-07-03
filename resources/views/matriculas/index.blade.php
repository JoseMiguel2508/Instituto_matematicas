@extends('layouts.app')

@section('title', 'Matrículas | Instituto de Matemáticas UPDS')
@section('page_title', 'Control de Matrículas')
@section('page_description', 'Registro de matrículas e inscripciones de estudiantes en periodos académicos')

@section('styles')
<style>
    /* Payment status banner */
    .pago-status {
        display: none;
        padding: 14px 18px;
        border-radius: 10px;
        margin: 14px 0;
        font-size: 0.9rem;
        font-weight: 600;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        animation: fadeInDown 0.3s ease;
    }
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .pago-status.visible { display: flex; }
    .pago-ok {
        background: rgba(16,185,129,0.12);
        border: 1px solid rgba(16,185,129,0.35);
        color: #34d399;
    }
    .pago-error {
        background: rgba(239,68,68,0.12);
        border: 1px solid rgba(239,68,68,0.35);
        color: #f87171;
    }
    .pago-loading {
        background: rgba(99,102,241,0.1);
        border: 1px solid rgba(99,102,241,0.25);
        color: #a5b4fc;
    }
    /* Prerequisite badges in group selector */
    .prereq-badge {
        display: inline-flex; align-items: center; gap: 4px;
        background: rgba(245,158,11,0.15);
        border: 1px solid rgba(245,158,11,0.3);
        color: #fbbf24;
        font-size: 0.72rem;
        padding: 2px 8px;
        border-radius: 999px;
        font-weight: 600;
        margin-left: 6px;
    }
    .prereq-info {
        display: none;
        background: rgba(245,158,11,0.06);
        border-left: 3px solid #f59e0b;
        border-top: 1px solid rgba(245,158,11,0.2);
        border-right: 1px solid rgba(245,158,11,0.2);
        border-bottom: 1px solid rgba(245,158,11,0.2);
        border-radius: 8px;
        padding: 10px 14px;
        margin-top: 8px;
        font-size: 0.82rem;
        color: #fbbf24;
        transition: border-left-color 0.3s ease, background 0.3s ease;
    }
    .prereq-info.visible { display: block; }
    .submit-btn-wrap { position: relative; }
    .submit-btn-wrap button:disabled {
        opacity: 0.5; cursor: not-allowed;
    }
    /* Step indicators */
    .flow-steps {
        display: flex;
        gap: 0;
        margin-bottom: 20px;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--glass-border);
    }
    .flow-step {
        flex: 1;
        padding: 12px 16px;
        font-size: 0.82rem;
        font-weight: 600;
        text-align: center;
        background: var(--glass-bg);
        color: var(--text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-right: 1px solid var(--glass-border);
    }
    .flow-step:last-child { border-right: none; }
    .flow-step.step-done {
        background: rgba(16,185,129,0.12);
        color: #34d399;
    }
    .flow-step.step-active {
        background: rgba(99,102,241,0.15);
        color: #a5b4fc;
    }
</style>
@endsection

@section('content')
    {{-- Flujo de proceso informativo --}}
    <div class="flow-steps">
        <div class="flow-step step-done">
            <i class="bi bi-person-check-fill"></i> 1. Registro de Persona
        </div>
        <div class="flow-step step-done">
            <i class="bi bi-cash-coin"></i> 2. Pago en Caja
        </div>
        <div class="flow-step step-active">
            <i class="bi bi-journal-check"></i> 3. Matrícula e Inscripción
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div style="display:flex; align-items:flex-start; gap:8px; margin-bottom:4px;">
                    <i class="bi bi-exclamation-triangle-fill" style="margin-top:2px; flex-shrink:0;"></i>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
        <button onclick="openModal()" class="btn btn-primary">
            <i class="bi bi-journal-plus"></i> Procesar Inscripción
        </button>
    </div>

    <!-- Active Matriculas List -->
    <div class="grid-card">
        <div class="grid-card-title">
            <span><i class="bi bi-journal-bookmark-fill" style="color: var(--accent-cyan);"></i> Historial de Matrículas e Inscripciones</span>
        </div>
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Estudiante</th>
                        <th>Periodo</th>
                        <th>Especialidad</th>
                        <th>Fecha Registro</th>
                        <th>Tipo</th>
                        <th>Registra</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($matriculas as $m)
                        <tr>
                            <td><code>#{{ $m->id_matricula }}</code></td>
                            <td style="font-weight: 500;">
                                {{ $m->estudiante && $m->estudiante->persona ? $m->estudiante->persona->nombre_completo : 'N/A' }}
                                <br>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">Cód. {{ $m->estudiante ? $m->estudiante->codigo_estudiante : '' }}</span>
                            </td>
                            <td><code>{{ $m->periodo ? $m->periodo->codigo : 'N/A' }}</code></td>
                            <td>{{ $m->especialidad ? $m->especialidad->nombre : 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($m->fecha_matricula)->format('d/m/Y H:i') }}</td>
                            <td>{{ $m->tipo }}</td>
                            <td style="font-size: 0.8rem; color: var(--text-secondary);">
                                {{ $m->usuario && $m->usuario->persona ? $m->usuario->persona->nombres : ($m->usuario ? $m->usuario->username : 'System') }}
                            </td>
                            <td>
                                <span class="badge {{ $m->estado === 'Activa' ? 'badge-success' : 'badge-danger' }}">{{ $m->estado }}</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline btn-sm" title="Editar Matrícula" style="padding: 4px 8px; color: var(--accent-blue); border-color: var(--accent-blue);" data-record="{{ json_encode($m) }}" onclick="openEditModal(this)">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                @if(Auth::user()->hasRole('Administrador'))
                                <form action="{{ route('matriculas.destroy', $m->id_matricula) }}" method="POST" style="display: inline-block; margin: 0;" onsubmit="return confirm('¿Está seguro de eliminar esta matrícula? Esto no se puede deshacer.')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline btn-sm" style="padding: 4px 8px; color: var(--danger); border-color: var(--danger);" title="Eliminar Matrícula">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 30px;">No hay matrículas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Process Matricula Modal -->
    <div class="modal-backdrop" id="matricula-modal">
        <div class="modal-card" style="max-width: 680px;">
            <div class="modal-header">
                <h3 style="font-size: 1.25rem;"><i class="bi bi-journal-plus" style="color: var(--accent-cyan);"></i> Procesar Inscripción a Curso</h3>
                <button onclick="closeModal()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.5rem;"><i class="bi bi-x"></i></button>
            </div>

            <form action="{{ route('matriculas.store') }}" method="POST" id="matricula-form">
                @csrf
                <div class="modal-body">

                    {{-- PASO 1: Seleccionar Estudiante --}}
                    <div style="background: rgba(99,102,241,0.07); border: 1px solid rgba(99,102,241,0.2); border-radius: 10px; padding: 14px; margin-bottom: 16px;">
                        <div style="font-size: 0.75rem; font-weight: 700; color: var(--accent-cyan); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px;">
                            <i class="bi bi-1-circle-fill"></i> Seleccionar Estudiante
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="ci_busqueda">Buscar Estudiante por CI</label>
                            <div style="display: flex; gap: 10px;">
                                <input type="text" id="ci_busqueda" class="form-control" placeholder="Ej. 10000000" style="flex: 1;">
                                <button type="button" class="btn btn-outline" onclick="buscarEstudiantePorCI()">Buscar</button>
                            </div>
                        </div>

                        <div class="form-group" id="div_estudiante_encontrado" style="display: none; margin-top: 10px;">
                            <label class="form-label" for="nombre_estudiante">Estudiante Encontrado</label>
                            <input type="text" id="nombre_estudiante" class="form-control" readonly style="background: rgba(0,0,0,0.1); font-weight: bold;">
                            <input type="hidden" id="id_estudiante" name="id_estudiante" required>
                        </div>

                        {{-- suggested course banner --}}
                        <div id="curso-sugerido-panel" style="margin-top: 10px; background: rgba(6, 182, 212, 0.1); border: 1px solid rgba(6, 182, 212, 0.3); border-radius: 6px; padding: 10px 14px; color: var(--accent-cyan); font-size: 0.85rem; display: none; align-items: center; gap: 8px;">
                            <i class="bi bi-info-circle-fill"></i>
                            <div>
                                <strong>Curso sugerido:</strong>
                                <span id="curso-sugerido-texto"></span>
                            </div>
                        </div>

                        {{-- Payment status indicator --}}
                        <div class="pago-status pago-loading" id="pago-loading">
                            <i class="bi bi-hourglass-split"></i> Verificando pago de matrícula...
                        </div>
                        <div class="pago-status pago-ok" id="pago-ok">
                            <i class="bi bi-shield-check"></i>
                            <div>
                                <div>✅ Pago de matrícula verificado — puede proceder a inscribirse</div>
                                <div id="pago-detalle" style="font-size:0.8rem; font-weight:400; margin-top:3px; color: #6ee7b7;"></div>
                            </div>
                        </div>
                        <div class="pago-status pago-error" id="pago-error">
                            <i class="bi bi-shield-x" style="font-size:1.4rem;"></i>
                            <div>
                                <div>⚠️ Sin pago de matrícula registrado</div>
                                <div id="pago-error-detalle" style="font-size:0.8rem; font-weight:400; margin-top:3px; color: #fca5a5;">El estudiante debe pagar en Caja primero (Módulo de Pagos) antes de poder inscribirse.</div>
                            </div>
                        </div>
                    </div>

                    {{-- PASO 2: Periodo y Especialidad --}}
                    <div style="background: rgba(99,102,241,0.07); border: 1px solid rgba(99,102,241,0.2); border-radius: 10px; padding: 14px; margin-bottom: 16px;">
                        <div style="font-size: 0.75rem; font-weight: 700; color: var(--accent-cyan); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px;">
                            <i class="bi bi-2-circle-fill"></i> Periodo y Especialidad
                        </div>
                        <div class="grid-2">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="id_periodo">Periodo Académico</label>
                                <select id="id_periodo" name="id_periodo" class="form-control" required>
                                    @foreach($periodos as $per)
                                        <option value="{{ $per->id_periodo }}" {{ old('id_periodo') == $per->id_periodo ? 'selected' : '' }}>
                                            {{ $per->nombre }} ({{ $per->codigo }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="id_especialidad">Especialidad</label>
                                <select id="id_especialidad" name="id_especialidad" class="form-control" required>
                                    @foreach($especialidades as $esp)
                                        <option value="{{ $esp->id_especialidad }}" {{ old('id_especialidad') == $esp->id_especialidad ? 'selected' : '' }}>
                                            {{ $esp->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- PASO 3: Curso / Grupo --}}
                    <div style="background: rgba(99,102,241,0.07); border: 1px solid rgba(99,102,241,0.2); border-radius: 10px; padding: 14px; margin-bottom: 16px;">
                        <div style="font-size: 0.75rem; font-weight: 700; color: var(--accent-cyan); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px;">
                            <i class="bi bi-3-circle-fill"></i> Curso a Inscribir
                        </div>
                        <div class="grid-2">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="turno_filtro">Filtrar por Turno</label>
                                <select id="turno_filtro" class="form-control" onchange="renderGrupos()">
                                    <option value="">Todos los turnos</option>
                                    <option value="Mañana">Mañana</option>
                                    <option value="Medio Día">Medio Día</option>
                                    <option value="Tarde">Tarde</option>
                                    <option value="Noche">Noche</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="id_grupo">Grupo / Curso Disponible</label>
                                <select id="id_grupo" name="id_grupo" class="form-control" required onchange="mostrarPrerequisitos(this)">
                                    <option value="">-- Seleccione Estudiante, Periodo y Especialidad --</option>
                                </select>
                                {{-- Prerequisite info panel --}}
                                <div class="prereq-info" id="prereq-info">
                                    <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px; font-size:0.83rem; color:#fbbf24;">
                                        <i class="bi bi-journal-check"></i>
                                        <strong>Estado académico del alumno — Prerrequisitos del curso:</strong>
                                    </div>
                                    <div id="prereq-list" style="color: var(--text-secondary);"></div>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="tipo">Tipo de Matrícula</label>
                                <select id="tipo" name="tipo" class="form-control" required>
                                    <option value="Regular" {{ old('tipo') == 'Regular' ? 'selected' : '' }}>Regular</option>
                                    <option value="Reincorporación" {{ old('tipo') == 'Reincorporación' ? 'selected' : '' }}>Reincorporación</option>
                                    <option value="Traslado" {{ old('tipo') == 'Traslado' ? 'selected' : '' }}>Traslado</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="observaciones">Observaciones (opcional)</label>
                        <textarea id="observaciones" name="observaciones" class="form-control"
                            placeholder="Detalles adicionales..." rows="2">{{ old('observaciones') }}</textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancelar</button>
                    <div class="submit-btn-wrap">
                        <button type="submit" id="submit-btn" class="btn btn-primary">
                            <i class="bi bi-journal-check"></i> Confirmar Inscripción
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal Editar Matrícula -->
    <div class="modal-backdrop" id="edit-modal">
        <div class="modal-card" style="max-width: 500px;">
            <div class="modal-header">
                <h3 style="font-size: 1.25rem;"><i class="bi bi-pencil-square" style="color: var(--accent-blue);"></i> Editar Matrícula</h3>
                <button onclick="closeEditModal()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.5rem;"><i class="bi bi-x"></i></button>
            </div>
            <form id="edit-form" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning" style="font-size: 0.85rem; margin-bottom: 15px;">
                        <i class="bi bi-info-circle-fill"></i> <strong>Nota:</strong> Para preservar la integridad del historial académico, sólo se permite editar el tipo de matrícula y las observaciones. Si el curso o el alumno son incorrectos, debe <strong>eliminar</strong> la matrícula y crear una nueva.
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="edit_tipo">Tipo de Matrícula</label>
                        <select id="edit_tipo" name="tipo" class="form-control" required>
                            <option value="Regular">Regular</option>
                            <option value="Reincorporación">Reincorporación</option>
                            <option value="Traslado">Traslado</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="edit_observaciones">Observaciones</label>
                        <textarea id="edit_observaciones" name="observaciones" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // ── Modal open / close ──────────────────────────────────────────────
    function openModal() {
        document.getElementById('matricula-modal').classList.add('open');
        // If old('id_estudiante') exists, trigger verification on load
        const est = document.getElementById('id_estudiante').value;
        if (est) onEstudianteSelected(est);
        const grp = document.getElementById('id_grupo');
        if (grp.value) mostrarPrerequisitos(grp);
    }
    function closeModal() {
        document.getElementById('matricula-modal').classList.remove('open');
    }

    function openEditModal(btn) {
        let record = JSON.parse(btn.getAttribute('data-record'));
        document.getElementById('edit-form').action = '/matriculas/update/' + record.id_matricula;
        document.getElementById('edit_tipo').value = record.tipo || 'Regular';
        document.getElementById('edit_observaciones').value = record.observaciones || '';
        document.getElementById('edit-modal').classList.add('open');
    }
    
    function closeEditModal() {
        document.getElementById('edit-modal').classList.remove('open');
    }

    // ── Student selected event ──────────────────────────────────────────
    async function buscarEstudiantePorCI() {
        const ci = document.getElementById('ci_busqueda').value.trim();
        if (!ci) {
            alert('Por favor ingrese un número de carnet de identidad.');
            return;
        }

        try {
            const response = await fetch(`/estudiantes/search-ci?ci=${ci}`);
            const data = await response.json();

            if (data.success) {
                document.getElementById('div_estudiante_encontrado').style.display = 'block';
                document.getElementById('nombre_estudiante').value = data.nombre_completo + ' (' + data.codigo_estudiante + ')';
                document.getElementById('id_estudiante').value = data.id_estudiante;
                
                // Disparar las verificaciones que antes dependían del onchange
                onEstudianteSelected(data.id_estudiante);
            } else {
                alert(data.message || 'Estudiante no encontrado');
                document.getElementById('div_estudiante_encontrado').style.display = 'none';
                document.getElementById('id_estudiante').value = '';
                onEstudianteSelected(''); 
            }
        } catch (error) {
            console.error('Error buscando estudiante:', error);
            alert('Hubo un error al buscar el estudiante.');
        }
    }

    function onEstudianteSelected(idEstudiante) {
        if (!idEstudiante) {
            ['pago-loading','pago-ok','pago-error'].forEach(id => {
                document.getElementById(id).classList.remove('visible');
            });
            return;
        }
        verificarPago(idEstudiante);
        cargarGruposDisponibles();
    }

    // ── Payment verification via AJAX ───────────────────────────────────
    const VERIFY_URL = '{{ url("/matriculas/verificar-pago") }}';

    function verificarPago(idEstudiante) {
        // Hide all banners
        ['pago-loading','pago-ok','pago-error'].forEach(id => {
            document.getElementById(id).classList.remove('visible');
        });

        if (!idEstudiante) return;

        const idPeriodo = document.getElementById('id_periodo').value || 1;
        document.getElementById('pago-loading').classList.add('visible');

        fetch(`${VERIFY_URL}/${idEstudiante}/${idPeriodo}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('pago-loading').classList.remove('visible');
            if (data.tiene_pago) {
                document.getElementById('pago-detalle').textContent = data.mensaje_exito || 'Solvencia verificada.';
                document.getElementById('pago-ok').classList.add('visible');
            } else {
                document.getElementById('pago-error-detalle').textContent = data.mensaje_error || 'Falta el pago de matrícula o mensualidad.';
                document.getElementById('pago-error').classList.add('visible');
            }
        })
        .catch(() => {
            document.getElementById('pago-loading').classList.remove('visible');
        });
    }

    // ── Cargar Grupos Disponibles Dinámicamente ─────────────────────────
    function cargarGruposDisponibles() {
        const idEstudiante = document.getElementById('id_estudiante').value;
        const idPeriodo = document.getElementById('id_periodo').value;
        const idEspecialidad = document.getElementById('id_especialidad').value;
        const grupoSelect = document.getElementById('id_grupo');
        const submitBtn = document.getElementById('submit-btn');
        const panelInfo = document.getElementById('curso-sugerido-panel');
        const textoInfo = document.getElementById('curso-sugerido-texto');

        // Limpiar select
        grupoSelect.innerHTML = '<option value="">-- Seleccione un Grupo --</option>';
        panelInfo.style.display = 'none';

        if (!idEstudiante || !idPeriodo || !idEspecialidad) {
            grupoSelect.innerHTML = '<option value="">-- Seleccione Estudiante, Periodo y Especialidad --</option>';
            submitBtn.disabled = true;
            return;
        }

        const GRUPOS_URL = `{{ url("/matriculas/grupos-disponibles") }}/${idEstudiante}/${idPeriodo}/${idEspecialidad}`;
        
        textoInfo.innerHTML = '<i class="bi bi-hourglass-split"></i> Cargando grupos y verificando historial...';
        panelInfo.style.display = 'flex';
        panelInfo.style.background = 'rgba(99,102,241,0.1)';
        panelInfo.style.borderColor = 'rgba(99,102,241,0.3)';
        submitBtn.disabled = true;

        fetch(GRUPOS_URL, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.loadedGrupos = data.grupos;
                renderGrupos();
                
                textoInfo.innerHTML = '<strong>Grupos filtrados:</strong> Se muestran solo las materias disponibles según el avance del estudiante.';
                panelInfo.style.background = 'rgba(16,185,129,0.1)';
                panelInfo.style.borderColor = 'rgba(16,185,129,0.3)';
                panelInfo.style.color = '#10b981';
                submitBtn.disabled = false;
            } else {
                // Mostrar error bloqueante (Ej: Curso en progreso)
                textoInfo.innerHTML = `<strong>Acción bloqueada:</strong> ${data.mensaje}`;
                panelInfo.style.background = 'rgba(239,68,68,0.1)';
                panelInfo.style.borderColor = 'rgba(239,68,68,0.3)';
                panelInfo.style.color = '#ef4444';
                submitBtn.disabled = true;
            }
        })
        .catch(err => {
            textoInfo.innerHTML = '<strong>Error de conexión.</strong> Intente nuevamente.';
            panelInfo.style.background = 'rgba(239,68,68,0.1)';
            panelInfo.style.borderColor = 'rgba(239,68,68,0.3)';
            panelInfo.style.color = '#ef4444';
        });
    }

    function renderGrupos() {
        const grupoSelect = document.getElementById('id_grupo');
        const turnoFiltro = document.getElementById('turno_filtro').value;
        
        grupoSelect.innerHTML = '<option value="">-- Seleccione un Grupo --</option>';
        
        if (window.loadedGrupos && window.loadedGrupos.length > 0) {
            let filtrados = window.loadedGrupos;
            if (turnoFiltro) {
                filtrados = filtrados.filter(g => g.turno === turnoFiltro);
            }
            
            if (filtrados.length === 0) {
                grupoSelect.innerHTML = '<option value="">-- No hay grupos en el turno seleccionado --</option>';
                document.getElementById('submit-btn').disabled = true;
            } else {
                filtrados.forEach(g => {
                    const prereqLabel = g.tiene_prerreq ? ' ⚠ Prerreq. OK' : '';
                    grupoSelect.innerHTML += `<option value="${g.id_grupo}">[${g.codigo_curso}] ${g.nombre_curso} — Grp.${g.numero_grupo} | Turno ${g.turno} | Doc. ${g.docente} ${prereqLabel}</option>`;
                });
                document.getElementById('submit-btn').disabled = false;
            }
        }
    }

    // Add event listeners to filter groups when Periodo or Especialidad change
    document.addEventListener('DOMContentLoaded', function() {
        const periodoSelect = document.getElementById('id_periodo');
        const especialidadSelect = document.getElementById('id_especialidad');
        
        if (periodoSelect) periodoSelect.addEventListener('change', cargarGruposDisponibles);
        if (especialidadSelect) especialidadSelect.addEventListener('change', cargarGruposDisponibles);
        
        if (periodoSelect && especialidadSelect && document.getElementById('id_estudiante').value) {
            cargarGruposDisponibles();
        }

        @if($errors->any())
            openModal();
        @endif

        // Restore prerequisite info for old input
        const grpSel = document.getElementById('id_grupo');
        if (grpSel.value) mostrarPrerequisitos(grpSel);
    });

    // Re-verify when period changes
    document.getElementById('id_periodo').addEventListener('change', function () {
        const est = document.getElementById('id_estudiante').value;
        if (est) onEstudianteSelected(est);
    });
</script>
@endsection
