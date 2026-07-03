@extends('layouts.app')

@section('title', 'Gestión de Grupos | Admin')

@section('page_title', 'Grupos de Estudio')
@section('page_description', 'Administra los grupos asignando cursos, docentes, aulas y períodos académicos.')

@section('content')

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom: 20px;">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger" style="margin-bottom: 20px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 12px 20px; border-radius: 8px;">
        <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger" style="margin-bottom: 20px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 12px 20px; border-radius: 8px;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid-card">
    <div class="grid-card-title">
        <span><i class="bi bi-collection" style="color: var(--accent-cyan); margin-right: 8px;"></i> Lista de Grupos</span>
        <button class="btn btn-primary" onclick="openModal('modalCreate')"><i class="bi bi-plus-lg"></i> Nuevo Grupo</button>
    </div>

    <div class="table-responsive">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Curso / Especialidad</th>
                    <th>Docente</th>
                    <th>Período</th>
                    <th>Aula</th>
                    <th>Grupo #</th>
                    <th>Cupo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($grupos as $g)
                    <tr>
                        <td>{{ $g->id_grupo }}</td>
                        <td>
                            <strong style="color: var(--text-primary);">{{ $g->curso ? $g->curso->nombre_curso : 'N/A' }}</strong><br>
                            <span style="font-size: 0.8rem; color: var(--text-secondary);">{{ $g->curso && $g->curso->especialidad ? $g->curso->especialidad->nombre : 'N/A' }}</span>
                        </td>
                        <td>{{ $g->docente && $g->docente->persona ? $g->docente->persona->nombres . ' ' . $g->docente->persona->paterno : 'N/A' }}</td>
                        <td>{{ $g->periodo ? $g->periodo->nombre : 'N/A' }}</td>
                        <td>{{ $g->aula ? $g->aula->codigo_aula : 'N/A' }}</td>
                        <td>
                            <span class="badge badge-secondary" style="font-size:0.8rem;">G-{{ $g->numero_grupo }}</span>
                            @if($g->horarios && $g->horarios->count() > 0)
                                <br>
                                <span style="font-size:0.75rem; color:var(--text-secondary); display:block; margin-top:4px;">
                                @foreach($g->horarios as $h)
                                    {{ $h->dia_semana }} {{ substr($h->hora_inicio, 0, 5) }}-{{ substr($h->hora_fin, 0, 5) }}<br>
                                @endforeach
                                </span>
                            @endif
                        </td>
                        <td>{{ $g->cupo_maximo }}</td>
                        <td>
                            @if($g->estado == 'Abierto')
                                <span class="badge badge-success">{{ $g->estado }}</span>
                            @elseif($g->estado == 'Cerrado')
                                <span class="badge badge-warning">{{ $g->estado }}</span>
                            @else
                                <span class="badge badge-secondary">{{ $g->estado }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <button class="btn btn-outline btn-sm" onclick="openEditModal({{ $g }})" title="Editar"><i class="bi bi-pencil-fill"></i></button>
                                <form action="{{ route('admin.grupos.destroy', $g->id_grupo) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este grupo?');" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger); border-color: rgba(239, 68, 68, 0.2);" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" style="text-align: center; color: var(--text-muted); padding: 30px;">No hay grupos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Create -->
<div id="modalCreate" class="modal-backdrop">
    <div class="modal-card" style="max-width: 600px;">
        <div class="modal-header">
            <h2>Nuevo Grupo</h2>
            <button onclick="closeModal('modalCreate')" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.5rem;"><i class="bi bi-x"></i></button>
        </div>
        <div class="modal-body">
            <form action="{{ route('admin.grupos.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Curso a Dictar</label>
                    <select name="id_curso" class="form-control" required>
                        <option value="">Seleccione un curso...</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id_curso }}">{{ $curso->nombre_curso }} ({{ $curso->especialidad ? $curso->especialidad->nombre : 'Sin Especialidad' }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Docente Asignado</label>
                    <select name="id_docente" class="form-control" required>
                        <option value="">Seleccione un docente...</option>
                        @foreach($docentes as $docente)
                            <option value="{{ $docente->id_docente }}">{{ $docente->persona ? $docente->persona->nombres . ' ' . $docente->persona->paterno : 'N/A' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Período Académico</label>
                        <select name="id_periodo" class="form-control" required>
                            @php $currentYear = date('Y'); @endphp
                            @foreach($periodos as $periodo)
                                @php 
                                    $isCurrentYear = (strpos($periodo->nombre, (string)$currentYear) !== false) || 
                                                     ($periodo->fecha_inicio && \Carbon\Carbon::parse($periodo->fecha_inicio)->format('Y') == $currentYear);
                                @endphp
                                <option value="{{ $periodo->id_periodo }}" {{ $isCurrentYear ? 'selected' : '' }}>
                                    {{ $periodo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Aula</label>
                        <select name="id_aula" class="form-control" required>
                            @foreach($aulas as $aula)
                                <option value="{{ $aula->id_aula }}">{{ $aula->codigo_aula }} (Cap: {{ $aula->capacidad }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label">Número de Grupo</label>
                        <input type="number" name="numero_grupo" class="form-control" placeholder="Ej: 1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cupo Máximo</label>
                        <input type="number" name="cupo_maximo" class="form-control" value="30" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-control">
                            <option value="Abierto">Abierto</option>
                            <option value="Cerrado">Cerrado</option>
                            <option value="Finalizado">Finalizado</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label class="form-label">Turno (Horario de Lunes a Viernes)</label>
                    <select name="turno" class="form-control" required>
                        <option value="">Seleccione un turno...</option>
                        <option value="Mañana">Mañana (08:30 - 10:30)</option>
                        <option value="Medio Día">Medio Día (11:30 - 13:30)</option>
                        <option value="Tarde">Tarde (14:30 - 16:30)</option>
                        <option value="Noche">Noche (19:30 - 21:30)</option>
                    </select>
                </div>

                <div class="modal-footer" style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalCreate')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Grupo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="modal-backdrop">
    <div class="modal-card" style="max-width: 600px;">
        <div class="modal-header">
            <h2>Editar Grupo</h2>
            <button onclick="closeModal('modalEdit')" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.5rem;"><i class="bi bi-x"></i></button>
        </div>
        <div class="modal-body">
            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label class="form-label">Curso a Dictar</label>
                    <select name="id_curso" id="edit_id_curso" class="form-control" required>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id_curso }}">{{ $curso->nombre_curso }} ({{ $curso->especialidad ? $curso->especialidad->nombre : 'Sin Especialidad' }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Docente Asignado</label>
                    <select name="id_docente" id="edit_id_docente" class="form-control" required>
                        @foreach($docentes as $docente)
                            <option value="{{ $docente->id_docente }}">{{ $docente->persona ? $docente->persona->nombres . ' ' . $docente->persona->paterno : 'N/A' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Período Académico</label>
                        <select name="id_periodo" id="edit_id_periodo" class="form-control" required>
                            @foreach($periodos as $periodo)
                                <option value="{{ $periodo->id_periodo }}">{{ $periodo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Aula</label>
                        <select name="id_aula" id="edit_id_aula" class="form-control" required>
                            @foreach($aulas as $aula)
                                <option value="{{ $aula->id_aula }}">{{ $aula->codigo_aula }} (Cap: {{ $aula->capacidad }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label">Número de Grupo</label>
                        <input type="number" name="numero_grupo" id="edit_numero_grupo" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cupo Máximo</label>
                        <input type="number" name="cupo_maximo" id="edit_cupo_maximo" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <select name="estado" id="edit_estado" class="form-control">
                            <option value="Abierto">Abierto</option>
                            <option value="Cerrado">Cerrado</option>
                            <option value="Finalizado">Finalizado</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label class="form-label">Turno (Horario de Lunes a Viernes)</label>
                    <select name="turno" id="edit_turno" class="form-control" required>
                        <option value="Mañana">Mañana (08:30 - 10:30)</option>
                        <option value="Medio Día">Medio Día (11:30 - 13:30)</option>
                        <option value="Tarde">Tarde (14:30 - 16:30)</option>
                        <option value="Noche">Noche (19:30 - 21:30)</option>
                    </select>
                </div>

                <div class="modal-footer" style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalEdit')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Grupo</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.add('open');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }

    function openEditModal(grupo) {
        document.getElementById('formEdit').action = '/admin/grupos/' + grupo.id_grupo;
        document.getElementById('edit_id_curso').value = grupo.id_curso;
        document.getElementById('edit_id_docente').value = grupo.id_docente;
        document.getElementById('edit_id_periodo').value = grupo.id_periodo;
        document.getElementById('edit_id_aula').value = grupo.id_aula;
        document.getElementById('edit_numero_grupo').value = grupo.numero_grupo;
        document.getElementById('edit_cupo_maximo').value = grupo.cupo_maximo;
        document.getElementById('edit_estado').value = grupo.estado;
        
        let turno = "Mañana";
        if (grupo.horarios && grupo.horarios.length > 0) {
            let hora = grupo.horarios[0].hora_inicio.substring(0,5);
            if (hora === '08:30') turno = 'Mañana';
            else if (hora === '11:30') turno = 'Medio Día';
            else if (hora === '14:30') turno = 'Tarde';
            else if (hora === '19:30') turno = 'Noche';
        }
        document.getElementById('edit_turno').value = turno;

        openModal('modalEdit');
    }
</script>
@endsection
