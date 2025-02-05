@extends('layouts.app')

@section('content')
<div class="container">
<h1 class="text-center mb-4">PACIENTES</h1>
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-end gap-2">
            <div id="exportButtonsContainer"></div>
            <button id="newPacienteButton" class="btn btn-primary">+ Nuevo</button>
        </div>
    </div>
    <table class="table table-bordered" id="pacientesTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Obra Social</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>


<!-- Modal para Crear/Editar -->
<div class="modal fade" id="pacienteModal" tabindex="-1">
    <div class="modal-dialog modal-lg"> <!-- Añadimos "modal-lg" para un tamaño más grande -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="pacienteForm">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="pacienteId" name="id">
                    <div id="validationErrors" class="alert alert-danger d-none">
                        <ul id="errorList"></ul>
                    </div>

                    <!-- Fila 1: Nombre, Apellido, Obra Social -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="col-md-4">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" required>
                        </div>
                        <div class="col-md-4">
                            <label for="osocial_id" class="form-label">Obra Social</label>
                            <select class="form-select" id="osocial_id" name="osocial_id" required>
                                <option value="">Selecciona una obra social</option>
                            </select>
                        </div>
                    </div>

                    <!-- Fila 2: CUIL, DNI, Dirección -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="cuil" class="form-label">CUIL</label>
                            <input type="text" class="form-control" id="cuil" name="cuil">
                        </div>
                        <div class="col-md-4">
                            <label for="dni" class="form-label">DNI</label>
                            <input type="text" class="form-control" id="dni" name="dni">
                        </div>
                        <div class="col-md-4">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion">
                        </div>
                    </div>

                    <!-- Fila 3: Teléfono 1, Teléfono 2, Número de Obra Social -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="telefono1" class="form-label">Teléfono 1</label>
                            <input type="text" class="form-control" id="telefono1" name="telefono1">
                        </div>
                        <div class="col-md-4">
                            <label for="telefono2" class="form-label">Teléfono 2</label>
                            <input type="text" class="form-control" id="telefono2" name="telefono2">
                        </div>
                        <div class="col-md-4">
                            <label for="nrosocial" class="form-label">Número de Obra Social</label>
                            <input type="text" class="form-control" id="nrosocial" name="nrosocial">
                        </div>
                    </div>

                    <!-- Fila 4: Observaciones -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal para Ver Detalles -->
<div class="modal fade" id="viewPacienteModal" tabindex="-1">
    <div class="modal-dialog modal-lg"> <!-- Añadimos "modal-lg" para un tamaño más grande -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Fila 1: Nombre, Apellido, Obra Social -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <p><strong>Nombre:</strong> <span id="viewNombre"></span></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Apellido:</strong> <span id="viewApellido"></span></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Obra Social:</strong> <span id="viewOsocial"></span></p>
                    </div>
                </div>

                <!-- Fila 2: CUIL, DNI, Dirección -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <p><strong>CUIL:</strong> <span id="viewCuil"></span></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>DNI:</strong> <span id="viewDni"></span></p>
                    </div>
                    
                </div>

                <!-- Fila 4: Observaciones -->
                <div class="row mb-3">
                <div class="row mb-12">
                        <p><strong>Dirección:</strong> <span id="viewDireccion"></span></p>
                    </div>

                </div>

              
                <!-- Fila 3: Teléfono 1, Teléfono 2, Número de Obra Social -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <p><strong>Teléfono 1:</strong> <span id="viewTelefono1"></span></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Teléfono 2:</strong> <span id="viewTelefono2"></span></p>
                    </div>
                    
                </div>
                <!-- Fila 4: Observaciones -->
                <div class="row mb-3">
                <div class="col-md-12">
                        <p><strong>Número de Obra Social:</strong> <span id="viewNrosocial"></span></p>
                    </div>
                </div>

                <!-- Fila 4: Observaciones -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <p><strong>Observaciones:</strong> <span id="viewObservaciones"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@push('styles')
<style>
    .invalid-feedback { display: block; color: #dc3545; }
    .is-invalid { border-color: #dc3545 !important; }
</style>
@endpush
@push('scripts')
<script>
// Configuración global de Axios
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 422) {
            showValidationErrors(error.response.data.errors);
        } else {
            const errorMessage = error.response?.data?.message || 'Error inesperado';
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage,
            });
        }
        return Promise.reject(error);
    }
);

function showValidationErrors(errors) {
    let errorMessages = '';
    for (let field in errors) {
        errorMessages += errors[field].join('<br>') + '<br>';
    }
    Swal.fire({
        icon: 'error',
        title: 'Errores de validación',
        html: errorMessages,
        confirmButtonText: 'Cerrar',
    });
}

function clearValidationErrors() {
    $('#validationErrors').addClass('d-none');
    $('#errorList').empty();
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
}

// Cargar pacientes en el select
function loadObrasSociales() {
    axios.get("{{ route('osocial.all') }}")
        .then(response => {
            const osocialSelect = $('#osocial_id');
            osocialSelect.empty();
            osocialSelect.append('<option value="">Selecciona una obra social</option>');
            response.data.forEach(osocial => {
                osocialSelect.append(`<option value="${osocial.id}">${osocial.nombre}</option>`);
            });
        })
        .catch(error => {
            console.error('Error al cargar pacientes:', error);
        });
}

$(document).ready(function () {
    // Cargar pacientes al iniciar
    loadObrasSociales();

    // DataTable
    const table = $('#pacientesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('pacientes.data') }}",
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'apellido' },
            { data: 'osocial.nombre', defaultContent: 'Sin obra social' },
            {
                data: 'id',
                render: function (data) {
                    return `
                        <button class="btn btn-sm btn-info view-btn" data-id="${data}">
                            <i class="fa fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-warning edit-btn" data-id="${data}">
                            <i class="fa fa-pencil-alt"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${data}">
                            <i class="fa fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        columnDefs: [
            {
                targets: -1,
                width: '120px',
                orderable: false,
                className: 'text-center',
            }
        ],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger'
            }
        ]
    });

    // Nuevo Paciente
    $('#newPacienteButton').click(() => {
        $('#pacienteForm')[0].reset();
        $('#pacienteId').val('');
        clearValidationErrors();
        new bootstrap.Modal('#pacienteModal').show();
    });

    // Editar Paciente
    $('#pacientesTable').on('click', '.edit-btn', function () {
        const pacienteId = $(this).data('id');
        axios.get(`/pacientes/show/${pacienteId}`)
            .then(response => {
                $('#pacienteId').val(response.data.id);
                $('#nombre').val(response.data.nombre);
                $('#apellido').val(response.data.apellido);
                $('#osocial_id').val(response.data.osocial_id);
                $('#cuil').val(response.data.cuil);
                $('#dni').val(response.data.dni);
                $('#direccion').val(response.data.direccion);
                $('#telefono1').val(response.data.telefono1);
                $('#telefono2').val(response.data.telefono2);
                $('#nrosocial').val(response.data.nrosocial);
                $('#observaciones').val(response.data.observaciones);
                clearValidationErrors();
                new bootstrap.Modal('#pacienteModal').show();
            });
    });

   // Enviar Formulario
$('#pacienteForm').submit(function (e) {
    e.preventDefault();
    clearValidationErrors();

    // Recopilar datos del formulario
    const formData = {
        nombre: $('#nombre').val(),
        apellido: $('#apellido').val(),
        osocial_id: $('#osocial_id').val(),
        cuil: $('#cuil').val(),
        dni: $('#dni').val(),
        direccion: $('#direccion').val(),
        telefono1: $('#telefono1').val(),
        telefono2: $('#telefono2').val(),
        nrosocial: $('#nrosocial').val(),
        observaciones: $('#observaciones').val(),
    };

    // Determinar el método y la URL
    const pacienteId = $('#pacienteId').val();
    const method = pacienteId ? 'put' : 'post';
    const url = pacienteId ? `/pacientes/update/${pacienteId}` : '/pacientes/store';

    // Enviar la solicitud
    axios[method](url, formData)
        .then(() => {
            table.ajax.reload(); // Recargar la tabla
            $('#pacienteModal').modal('hide'); // Cerrar el modal
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'Operación realizada correctamente',
                timer: 1500,
            });
        })
        .catch(error => {
            if (error.response?.status === 422) {
                // Mostrar errores de validación
                showValidationErrors(error.response.data.errors);
            } else {
                // Mostrar otros errores
                const errorMessage = error.response?.data?.message || 'Error inesperado';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                });
            }
        });
});

    // Ver Detalles
    $('#pacientesTable').on('click', '.view-btn', function () {
        const pacienteId = $(this).data('id');
        axios.get(`/pacientes/show/${pacienteId}`)
            .then(response => {
                $('#viewNombre').text(response.data.nombre);
                $('#viewApellido').text(response.data.apellido);
                $('#viewOsocial').text(response.data.osocial?.nombre || 'Sin obra social');
                $('#viewCuil').text(response.data.cuil || 'No especificado');
                $('#viewDni').text(response.data.dni || 'No especificado');
                $('#viewDireccion').text(response.data.direccion || 'No especificado');
                $('#viewTelefono1').text(response.data.telefono1 || 'No especificado');
                $('#viewTelefono2').text(response.data.telefono2 || 'No especificado');
                $('#viewNrosocial').text(response.data.nrosocial || 'No especificado');
                $('#viewObservaciones').text(response.data.observaciones || 'No especificado');
                new bootstrap.Modal('#viewPacienteModal').show();
            });
    });

    // Eliminar Paciente
    $('#pacientesTable').on('click', '.delete-btn', function () {
        const pacienteId = $(this).data('id');
        Swal.fire({
            title: '¿Eliminar paciente?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/pacientes/destroy/${pacienteId}`)
                    .then(() => {
                        table.ajax.reload();
                        Swal.fire('¡Eliminado!', '', 'success');
                    });
            }
        });
    });
});
</script>
@endpush
@endsection