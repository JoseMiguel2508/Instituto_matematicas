@extends('layouts.app')

@section('title', 'Control de Pagos | Instituto de Matemáticas UPDS')
@section('page_title', 'Control de Pagos')
@section('page_description', 'Registro y control de cobranzas por conceptos académicos')

@section('styles')
<style>
    .deuda-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 8px;
        margin-bottom: 8px;
        transition: all 0.2s ease;
    }
    .deuda-item:hover {
        background: rgba(99,102,241,0.05);
        border-color: rgba(99,102,241,0.3);
    }
    .deuda-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .deuda-title {
        font-weight: 600;
        color: var(--text-color);
    }
    .deuda-periodo {
        font-size: 0.75rem;
        color: var(--text-muted);
    }
    .deuda-monto {
        font-weight: 700;
        color: var(--accent-cyan);
    }
    .deudas-container {
        max-height: 250px;
        overflow-y: auto;
        padding-right: 5px;
        margin-bottom: 15px;
    }
    .no-deudas {
        padding: 20px;
        text-align: center;
        color: var(--text-muted);
        background: var(--glass-bg);
        border-radius: 8px;
        border: 1px dashed var(--glass-border);
    }
</style>
@endsection

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

    <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
        <button onclick="openModal()" class="btn btn-primary">
            <i class="bi bi-cash-stack"></i> Registrar Cobranza
        </button>
    </div>

    <!-- Payments List -->
    <div class="grid-card">
        <div class="grid-card-title">
            <span><i class="bi bi-credit-card-fill" style="color: var(--accent-cyan);"></i> Comprobantes de Pago</span>
        </div>
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Nro Boleta</th>
                        <th>Estudiante</th>
                        <th>Tipo Comprobante</th>
                        <th>Método</th>
                        <th>Total</th>
                        <th>Fecha Pago</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pagos as $p)
                        <tr>
                            <td><code>{{ $p->numero_comprobante }}</code></td>
                            <td style="font-weight: 500;">
                                {{ $p->estudiante && $p->estudiante->persona ? $p->estudiante->persona->nombre_completo : 'N/A' }}
                                <br>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $p->estudiante ? $p->estudiante->codigo_estudiante : '' }}</span>
                            </td>
                            <td>{{ $p->tipo_comprobante }}</td>
                            <td>{{ $p->metodo_pago }}</td>
                            <td style="font-weight: 700; color: var(--accent-cyan);">Bs. {{ number_format($p->monto_total, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->fecha_pago)->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge {{ $p->estado === 'Registrado' ? 'badge-success' : 'badge-danger' }}">{{ $p->estado }}</span>
                            </td>
                            <td>
                                <a href="{{ route('pagos.showReceipt', $p->id_pago) }}" target="_blank" class="btn btn-outline btn-sm" style="padding: 4px 10px; font-size: 0.75rem;" title="Imprimir">
                                    <i class="bi bi-printer"></i>
                                </a>
                                <button type="button" class="btn btn-outline btn-sm" title="Editar Pago" style="padding: 4px 8px; color: var(--accent-blue); border-color: var(--accent-blue);" data-record="{{ json_encode($p) }}" onclick="openEditModal(this)">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                @if(Auth::user()->hasRole('Administrador'))
                                    <form action="{{ route('pagos.destroy', $p->id_pago) }}" method="POST" style="display: inline-block; margin: 0;" onsubmit="return confirm('¿Está seguro de eliminar este pago? Las deudas volverán a estado pendiente.')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline btn-sm" style="padding: 4px 8px; color: var(--danger); border-color: var(--danger);" title="Eliminar Pago">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">No hay cobranzas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Process Cobranza Modal -->
    <div class="modal-backdrop" id="payment-modal">
        <div class="modal-card" style="max-width: 600px;">
            <div class="modal-header">
                <h3 style="font-size: 1.25rem;"><i class="bi bi-cash-coin" style="color: var(--accent-cyan);"></i> Registrar Nueva Cobranza</h3>
                <button onclick="closeModal()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.5rem;"><i class="bi bi-x"></i></button>
            </div>
            <form action="{{ route('pagos.store') }}" method="POST" id="pago-form">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="ci_busqueda">Buscar Estudiante por CI</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" id="ci_busqueda" class="form-control" placeholder="Ej. 10000000" style="flex: 1;">
                            <button type="button" class="btn btn-outline" onclick="buscarEstudiantePorCI()">Buscar</button>
                        </div>
                    </div>

                    <div class="form-group" id="div_estudiante_encontrado" style="display: none;">
                        <label class="form-label" for="nombre_estudiante">Estudiante Encontrado</label>
                        <input type="text" id="nombre_estudiante" class="form-control" readonly style="background: rgba(0,0,0,0.1); font-weight: bold;">
                        <input type="hidden" id="id_estudiante" name="id_estudiante" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Deudas Pendientes</label>
                        <div id="deudas-loading" style="display: none; padding: 10px; color: var(--text-muted);">
                            <i class="bi bi-arrow-repeat spin"></i> Buscando deudas...
                        </div>
                        <div id="deudas-container" class="deudas-container">
                            <div class="no-deudas">Seleccione un estudiante para ver sus deudas pendientes.</div>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="tipo_comprobante">Tipo de Comprobante</label>
                            <select id="tipo_comprobante" name="tipo_comprobante" class="form-control" required>
                                <option value="Boleta">Boleta</option>
                                <option value="Factura">Factura</option>
                                <option value="Ticket">Ticket</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="metodo_pago">Método de Pago</label>
                            <select id="metodo_pago" name="metodo_pago" class="form-control" required onchange="handleMetodoPago()">
                                <option value="Efectivo">Efectivo</option>
                                <option value="QR">QR</option>
                                <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                            </select>
                        </div>
                    </div>

                    <!-- Detalles para Efectivo -->
                    <div id="div_efectivo" style="display: block; background: rgba(34,197,94,0.05); border: 1px solid rgba(34,197,94,0.2); padding: 15px; border-radius: var(--radius-sm); margin-bottom: 20px;">
                        <div class="grid-2">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" for="monto_recibido">Monto Recibido (Bs.)</label>
                                <input type="number" step="0.01" min="0" id="monto_recibido" class="form-control" placeholder="0.00" oninput="calcularCambio()">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">Cambio a Devolver</label>
                                <div id="cambio_devolver" style="font-size: 1.25rem; font-weight: 700; color: var(--success); padding: 8px 12px; background: rgba(0,0,0,0.2); border-radius: 4px;">
                                    Bs. 0.00
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detalles para QR -->
                    <div id="div_qr" style="display: none; background: rgba(6,182,212,0.05); border: 1px solid rgba(6,182,212,0.2); padding: 15px; border-radius: var(--radius-sm); margin-bottom: 20px; text-align: center;">
                        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 10px;">Escanea el código QR para realizar el pago</p>
                        <!-- Se asume que el usuario subirá su QR en public/img/qr_pago.jpg -->
                        <img src="{{ asset('img/qr_pago.jpg') }}" alt="Código QR de Pago" style="max-width: 200px; border-radius: 8px; border: 2px solid var(--accent-cyan); padding: 4px; background: white;">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="monto_total">Monto Total a Cobrar (Bs.)</label>
                        <input type="number" step="0.01" min="0.01" id="monto_total" name="monto_total" class="form-control" placeholder="0.00" readonly required style="background: rgba(0,0,0,0.1); font-weight: 700;">
                        <small style="color: var(--text-muted);">El monto se calcula automáticamente al seleccionar las deudas a pagar.</small>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="observaciones">Observaciones</label>
                        <input type="text" id="observaciones" name="observaciones" class="form-control" placeholder="Detalles o comentarios del pago...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit" disabled>Registrar Pago</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal Editar Pago -->
    <div class="modal-backdrop" id="edit-modal">
        <div class="modal-card" style="max-width: 500px;">
            <div class="modal-header">
                <h3 style="font-size: 1.25rem;"><i class="bi bi-pencil-square" style="color: var(--accent-blue);"></i> Editar Detalles de Pago</h3>
                <button onclick="closeEditModal()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.5rem;"><i class="bi bi-x"></i></button>
            </div>
            <form id="edit-form" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning" style="font-size: 0.85rem; margin-bottom: 15px;">
                        <i class="bi bi-info-circle-fill"></i> <strong>Nota:</strong> Solo se permite editar datos del comprobante para preservar la integridad financiera. Si el monto o el estudiante es incorrecto, el Administrador debe eliminar el pago.
                    </div>
                    
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="edit_tipo_comprobante">Tipo de Comprobante</label>
                            <select id="edit_tipo_comprobante" name="tipo_comprobante" class="form-control" required>
                                <option value="Boleta">Boleta</option>
                                <option value="Factura">Factura</option>
                                <option value="Ticket">Ticket</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit_numero_comprobante">Número Comprobante</label>
                            <input type="text" id="edit_numero_comprobante" name="numero_comprobante" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit_metodo_pago">Método de Pago</label>
                        <select id="edit_metodo_pago" name="metodo_pago" class="form-control" required>
                            <option value="Efectivo">Efectivo</option>
                            <option value="QR">QR</option>
                            <option value="Transferencia Bancaria">Transferencia Bancaria</option>
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
    function openModal() {
        document.getElementById('payment-modal').classList.add('open');
    }

    function closeModal() {
        document.getElementById('payment-modal').classList.remove('open');
    }

    function openEditModal(btn) {
        let record = JSON.parse(btn.getAttribute('data-record'));
        document.getElementById('edit-form').action = '/pagos/update/' + record.id_pago;
        document.getElementById('edit_tipo_comprobante').value = record.tipo_comprobante || 'Boleta';
        document.getElementById('edit_numero_comprobante').value = record.numero_comprobante || '';
        document.getElementById('edit_metodo_pago').value = record.metodo_pago || 'Efectivo';
        document.getElementById('edit_observaciones').value = record.observaciones || '';
        document.getElementById('edit-modal').classList.add('open');
    }
    
    function closeEditModal() {
        document.getElementById('edit-modal').classList.remove('open');
    }

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
                
                // Cargar deudas del estudiante encontrado
                cargarDeudas(data.id_estudiante);
            } else {
                alert(data.message || 'Estudiante no encontrado');
                document.getElementById('div_estudiante_encontrado').style.display = 'none';
                document.getElementById('id_estudiante').value = '';
                cargarDeudas(''); // Limpiar deudas
            }
        } catch (error) {
            console.error('Error buscando estudiante:', error);
            alert('Hubo un error al buscar el estudiante.');
        }
    }

    function cargarDeudas(idEstudiante) {
        const container = document.getElementById('deudas-container');
        const loading = document.getElementById('deudas-loading');
        const btnSubmit = document.getElementById('btn-submit');
        const montoInput = document.getElementById('monto_total');
        
        container.innerHTML = '';
        montoInput.value = '';
        btnSubmit.disabled = true;

        if (!idEstudiante) {
            container.innerHTML = '<div class="no-deudas">Seleccione un estudiante para ver sus deudas pendientes.</div>';
            return;
        }

        loading.style.display = 'block';

        fetch(`/pagos/deudas/${idEstudiante}`)
            .then(res => res.json())
            .then(deudas => {
                loading.style.display = 'none';
                
                if (deudas.length === 0) {
                    container.innerHTML = '<div class="no-deudas">✅ Este estudiante no tiene deudas pendientes.</div>';
                    return;
                }

                deudas.forEach(deuda => {
                    const div = document.createElement('div');
                    div.className = 'deuda-item';
                    div.innerHTML = `
                        <div style="display:flex; align-items:center; gap:12px;">
                            <input type="checkbox" name="deudas[]" value="${deuda.id_deuda}" data-monto="${deuda.monto}" onchange="calcularTotal()" class="form-control" style="width: 20px; height: 20px; margin:0; cursor:pointer;">
                            <div class="deuda-info">
                                <span class="deuda-title">${deuda.concepto.nombre}</span>
                                <span class="deuda-periodo">Periodo: ${deuda.periodo.codigo} | Vence: ${deuda.fecha_generacion.substring(0,10)}</span>
                            </div>
                        </div>
                        <div class="deuda-monto">Bs. ${parseFloat(deuda.monto).toFixed(2)}</div>
                    `;
                    container.appendChild(div);
                });
            })
            .catch(err => {
                loading.style.display = 'none';
                container.innerHTML = '<div class="no-deudas" style="color:#ef4444;">Error al cargar las deudas.</div>';
            });
    }

    function calcularTotal() {
        const checkboxes = document.querySelectorAll('input[name="deudas[]"]:checked');
        let total = 0;
        checkboxes.forEach(cb => {
            total += parseFloat(cb.getAttribute('data-monto'));
        });
        
        const montoInput = document.getElementById('monto_total');
        montoInput.value = total > 0 ? total.toFixed(2) : '';
        
        document.getElementById('btn-submit').disabled = total === 0;
        calcularCambio();
    }

    function handleMetodoPago() {
        const metodo = document.getElementById('metodo_pago').value;
        const divEfectivo = document.getElementById('div_efectivo');
        const divQr = document.getElementById('div_qr');

        if (metodo === 'Efectivo') {
            divEfectivo.style.display = 'block';
            divQr.style.display = 'none';
        } else if (metodo === 'QR') {
            divEfectivo.style.display = 'none';
            divQr.style.display = 'block';
        } else {
            divEfectivo.style.display = 'none';
            divQr.style.display = 'none';
        }
    }

    function calcularCambio() {
        const total = parseFloat(document.getElementById('monto_total').value) || 0;
        const recibido = parseFloat(document.getElementById('monto_recibido').value) || 0;
        const cambioDiv = document.getElementById('cambio_devolver');
        
        if (recibido >= total && total > 0) {
            const cambio = recibido - total;
            cambioDiv.innerText = 'Bs. ' + cambio.toFixed(2);
            cambioDiv.style.color = 'var(--success)';
        } else if (recibido > 0 && recibido < total) {
            cambioDiv.innerText = 'Monto insuficiente';
            cambioDiv.style.color = 'var(--danger)';
        } else {
            cambioDiv.innerText = 'Bs. 0.00';
            cambioDiv.style.color = 'var(--success)';
        }
    }

    // Inicializar visualización del método de pago
    document.addEventListener('DOMContentLoaded', function() {
        handleMetodoPago();
    });
</script>
@endsection
