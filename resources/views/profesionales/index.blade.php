@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">PROFESIONALES</h1>
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-end gap-2">
            <div id="exportButtonsContainer"></div>
            <button id="newProfesionalButton" class="btn btn-primary">+ Nuevo</button>
        </div>
    </div>
    <table class="table table-bordered" id="profesionalesTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Profesión</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal para Crear/Editar -->
<div class="modal fade" id="profesionalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Profesional</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="profesionalForm">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="profesionalId" name="id">
                    <div id="validationErrors" class="alert alert-danger d-none">
                        <ul id="errorList"></ul>
                    </div>

                    <!-- Fila 1: Nombre, Apellido, Profesión -->
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
                            <label for="profesion_id" class="form-label">Profesión</label>
                            <select class="form-select" id="profesion_id" name="profesion_id" required>
                                <option value="">Selecciona una profesión</option>
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

                    <!-- Fila 3: Teléfono 1, Teléfono 2 -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="telefono1" class="form-label">Teléfono 1</label>
                            <input type="text" class="form-control" id="telefono1" name="telefono1">
                        </div>
                        <div class="col-md-4">
                            <label for="telefono2" class="form-label">Teléfono 2</label>
                            <input type="text" class="form-control" id="telefono2" name="telefono2">
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
<div class="modal fade" id="viewProfesionalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Profesional</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Fila 1: Nombre, Apellido, Profesión -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <p><strong>Nombre:</strong> <span id="viewNombre"></span></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Apellido:</strong> <span id="viewApellido"></span></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Profesión:</strong> <span id="viewProfesion"></span></p>
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

                <!-- Fila 3: Teléfono 1, Teléfono 2 -->
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

// Cargar profesiones en el select
function loadProfesiones() {
    axios.get("{{ route('profesionales.profesion.all') }}")
        .then(response => {
            const profesionSelect = $('#profesion_id');
            profesionSelect.empty();
            profesionSelect.append('<option value="">Selecciona una profesión</option>');
            response.data.forEach(profesion => {
                profesionSelect.append(`<option value="${profesion.id}">${profesion.nombre}</option>`);
            });
        })
        .catch(error => {
            console.error('Error al cargar profesiones:', error);
        });
}

$(document).ready(function () {
    // Cargar profesiones al iniciar
    loadProfesiones();

    // DataTable
    const table = $('#profesionalesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('profesionales.indexData') }}',
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'apellido' },
            { data: 'profesion.nombre' },
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
                width: '160px',
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



    // Abrir el modal para agregar un nuevo profesional
    $('#newProfesionalButton').click(function () {
        $('#profesionalModal .modal-title').text('Nuevo Profesional');
        clearValidationErrors();
        $('#profesionalForm')[0].reset();
        $('#profesionalId').val('');
        $('#profesionalModal').modal('show');
    });

    // Enviar el formulario de crear o editar
    $('#profesionalForm').submit(function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const profesionalId = $('#profesionalId').val();
        const url = profesionalId ? `{{ route('profesionales.update', '') }}/${profesionalId}` : '{{ route('profesionales.store') }}';
        const method = profesionalId ? 'PUT' : 'POST';

        axios({
            method: method,
            url: url,
            data: formData,
            headers: { 'Content-Type': 'multipart/form-data' },
        })
        .then(response => {
            $('#profesionalModal').modal('hide');
            table.ajax.reload();
            Swal.fire('Éxito', 'Profesional guardado correctamente.', 'success');
        })
        .catch(error => {
            if (error.response?.status === 422) {
                showValidationErrors(error.response.data.errors);
            }
        });
    });

    // Ver detalles de un profesional
    $(document).on('click', '.viewProfesionalButton', function () {
        const profesionalId = $(this).data('id');
        axios.get(`{{ route('profesionales.show', '') }}/${profesionalId}`)
            .then(response => {
                const profesional = response.data;
                $('#viewNombre').text(profesional.nombre);
                $('#viewApellido').text(profesional.apellido);
                $('#viewProfesion').text(profesional.profesion.nombre);
                $('#viewCuil').text(profesional.cuil);
                $('#viewDni').text(profesional.dni);
                $('#viewTelefono1').text(profesional.telefono1);
                $('#viewTelefono2').text(profesional.telefono2);
                $('#viewObservaciones').text(profesional.observaciones);
                $('#viewProfesionalModal').modal('show');
            })
            .catch(error => console.error('Error al obtener detalles del profesional:', error));
    });

    // Eliminar un profesional
    $(document).on('click', '.deleteProfesionalButton', function () {
        const profesionalId = $(this).data('id');
        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás revertir esta acción",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminarlo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`{{ route('profesionales.destroy', '') }}/${profesionalId}`)
                    .then(response => {
                        Swal.fire('Eliminado!', 'El profesional ha sido eliminado.', 'success');
                        table.ajax.reload();
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Hubo un problema al eliminar al profesional.', 'error');
                    });
            }
        });
    });
});
</script>
@endpush
