@extends('layouts.app')

@section('title', 'Registro de Notas | Instituto de Matemáticas UPDS')
@section('page_title', 'Registro de Calificaciones')
@section('page_description', 'Registro de notas finales e indicaciones de aprobación de alumnos por curso')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li><i class="bi bi-exclamation-triangle-fill"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Selector de Grupo -->
    <div class="grid-card" style="margin-bottom: 25px; padding: 20px;">
        <form method="GET" action="{{ route('notas.index') }}" style="display: flex; gap: 15px; align-items: flex-end;">
            <div style="flex-grow: 1;">
                <label class="form-label" for="id_grupo">Seleccionar Grupo de Asignatura</label>
                <select name="id_grupo" id="id_grupo" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Seleccione un Grupo para ver sus alumnos --</option>
                    @foreach($availableGroups as $grp)
                        <option value="{{ $grp->id_grupo }}" {{ request('id_grupo') == $grp->id_grupo ? 'selected' : '' }}>
                            {{ $grp->periodo->codigo }} | {{ $grp->curso->codigo_curso }} - {{ $grp->curso->nombre_curso }} (Grupo {{ $grp->numero_grupo }}) 
                            - Estado: {{ $grp->estado }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="height: 42px;">
                <i class="bi bi-search"></i> Ver Notas
            </button>
        </form>
    </div>

    @forelse($grupos as $id_grupo => $inscritos)
        @php
            $grupoInfo = $inscritos->first()->grupo;
            $curso = $grupoInfo->curso;
            $docente = $grupoInfo->docente->persona;
            $horarios = $grupoInfo->horarios;
        @endphp

        <div class="grid-card" style="margin-bottom: 25px;">
            <div class="grid-card-title" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <i class="bi bi-journal-bookmark-fill" style="color: var(--accent-cyan); font-size:1.5rem;"></i>
                    <div>
                        <h3 style="margin:0; font-size:1.2rem; color:var(--text-color);">{{ $curso->codigo_curso }} - {{ $curso->nombre_curso }}</h3>
                        <div style="font-size:0.85rem; color:var(--text-muted); font-weight:400; margin-top:2px;">
                            Grupo {{ $grupoInfo->numero_grupo }} | Docente: {{ $docente->nombres }} {{ $docente->apellidos }}
                        </div>
                    </div>
                </div>
                <div style="background:rgba(99,102,241,0.1); border:1px solid rgba(99,102,241,0.2); border-radius:8px; padding:6px 12px; font-size:0.8rem;">
                    @if($horarios->count() > 0)
                        <div style="color:var(--accent-cyan); font-weight:600; margin-bottom:2px;"><i class="bi bi-clock-history"></i> Horarios:</div>
                        @foreach($horarios as $h)
                            <div style="color:var(--text-secondary);">{{ $h->dia_semana }}: {{ substr($h->hora_inicio, 0, 5) }} - {{ substr($h->hora_fin, 0, 5) }}</div>
                        @endforeach
                    @else
                        <div style="color:var(--text-muted);">Sin horarios asignados</div>
                    @endif
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>Alumno (DNI / Cód)</th>
                            <th>Calificación</th>
                            <th>Estado</th>
                            <th>Registrado por</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inscritos as $index => $item)
                            <tr>
                                <td style="color: var(--text-muted); width: 40px;">{{ $index + 1 }}</td>
                                <td style="font-weight: 500;">
                                    {{ $item->inscripcion->matricula->estudiante->persona->nombre_completo }}
                                    <br>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">
                                        DNI: {{ $item->inscripcion->matricula->estudiante->persona->numero_documento }} | 
                                        Cód: {{ $item->inscripcion->matricula->estudiante->codigo_estudiante }}
                                    </span>
                                </td>
                                <td style="font-weight: 700; font-size: 1.1rem; color: var(--accent-cyan);">
                                    {{ $item->notaFinal ? number_format($item->notaFinal->nota, 2) : '-' }}
                                </td>
                                <td>
                                    @if($item->notaFinal)
                                        <span class="badge {{ $item->notaFinal->estado === 'Aprobado' ? 'badge-success' : 'badge-danger' }}">
                                            {{ $item->notaFinal->estado }}
                                        </span>
                                    @else
                                        <span class="badge badge-warning">Sin Registrar</span>
                                    @endif
                                </td>
                                <td style="font-size: 0.8rem; color: var(--text-secondary);">
                                    {{ $item->notaFinal && $item->notaFinal->usuario ? $item->notaFinal->usuario->persona->nombres : '-' }}
                                </td>
                                <td>
                                    <button onclick="openGradeModal('{{ $item->id_detalle_inscripcion }}', '{{ $item->inscripcion->matricula->estudiante->persona->nombre_completo }}', '{{ $curso->nombre_curso }} - Grp.{{ $grupoInfo->numero_grupo }}', '{{ $item->notaFinal ? $item->notaFinal->nota : '' }}', '{{ $item->notaFinal ? $item->notaFinal->observaciones : '' }}')" class="btn btn-outline btn-sm">
                                        <i class="bi bi-pencil-square"></i> {{ $item->notaFinal ? 'Editar Nota' : 'Calificar' }}
                                    </button>
                                    @if($item->notaFinal && Auth::user()->hasRole('Administrador'))
                                        <form action="{{ route('notas.destroy', $item->notaFinal->id_nota_final) }}" method="POST" style="display: inline-block; margin: 0;" onsubmit="return confirm('¿Está seguro de eliminar esta nota? Esto no se puede deshacer.')">
                                            @csrf
                                            <button type="submit" class="btn btn-outline btn-sm" style="padding: 4px 8px; color: var(--danger); border-color: var(--danger);" title="Eliminar Nota">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        @if(request()->has('id_grupo') && request('id_grupo') != '')
            <div class="grid-card" style="text-align: center; padding: 50px 20px;">
                <i class="bi bi-journal-x" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 15px; display: block;"></i>
                <h3 style="color: var(--text-color);">Grupo sin alumnos</h3>
                <p style="color: var(--text-secondary);">No se encontraron alumnos inscritos en el grupo seleccionado.</p>
            </div>
        @else
            <div class="grid-card" style="text-align: center; padding: 50px 20px;">
                <i class="bi bi-hand-index-thumb" style="font-size: 3rem; color: var(--accent-cyan); margin-bottom: 15px; display: block;"></i>
                <h3 style="color: var(--text-color);">Seleccione un Grupo</h3>
                <p style="color: var(--text-secondary);">Por favor, utilice el menú desplegable superior para seleccionar el grupo que desea calificar.</p>
            </div>
        @endif
    @endforelse

    <!-- Save Grade Modal -->
    <div class="modal-backdrop" id="grade-modal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.25rem;"><i class="bi bi-award" style="color: var(--accent-cyan);"></i> Registrar Calificación</h3>
                <button onclick="closeGradeModal()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.5rem;"><i class="bi bi-x"></i></button>
            </div>
            <form action="{{ route('notas.store') }}" method="POST">
                @csrf
                <input type="hidden" id="id_detalle_inscripcion" name="id_detalle_inscripcion">
                
                <div class="modal-body">
                    <div style="background-color: rgba(255,255,255,0.02); border: 1px solid var(--card-border); padding: 15px; border-radius: var(--radius-sm); margin-bottom: 20px;">
                        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 5px;">Estudiante:</p>
                        <h4 id="display-student" style="margin-bottom: 10px; color: var(--text-primary);"></h4>
                        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 5px;">Curso:</p>
                        <h5 id="display-course" style="color: var(--text-primary);"></h5>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="nota">Calificación Final (0.00 - 20.00)</label>
                        <input type="number" step="0.01" min="0" max="20" id="nota" name="nota" class="form-control" placeholder="Ej. 14.50" required>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px;">Aprobación automática con nota mayor o igual a 11.00</p>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="observaciones">Observaciones</label>
                        <input type="text" id="observaciones" name="observaciones" class="form-control" placeholder="Detalles de evaluación...">
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" onclick="closeGradeModal()" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Nota</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function openGradeModal(detailId, studentName, courseName, currentGrade, observations) {
            document.getElementById('id_detalle_inscripcion').value = detailId;
            document.getElementById('display-student').innerText = studentName;
            document.getElementById('display-course').innerText = courseName;
            document.getElementById('nota').value = currentGrade;
            document.getElementById('observaciones').value = observations;
            
            document.getElementById('grade-modal').classList.add('open');
            document.getElementById('nota').focus();
        }

        function closeGradeModal() {
            document.getElementById('grade-modal').classList.remove('open');
        }
    </script>
@endsection
