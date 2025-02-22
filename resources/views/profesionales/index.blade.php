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

                    <!-- Campos del formulario -->
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
                <div class="row mb-3">
                    <div class="col-md-4">
                        <p><strong>CUIL:</strong> <span id="viewCuil"></span></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>DNI:</strong> <span id="viewDni"></span></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Dirección:</strong> <span id="viewDireccion"></span></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <p><strong>Teléfono 1:</strong> <span id="viewTelefono1"></span></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Teléfono 2:</strong> <span id="viewTelefono2"></span></p>
                    </div>
                    <div class="col-md-4">
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
$(document).ready(function () {
    // Cargar profesiones al iniciar
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

    // Inicializar DataTables
    const table = $('#profesionalesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('profesionales.indexData') }}",
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'apellido' },
            { data: 'profesion_nombre' },
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

    // Cargar profesiones al iniciar
    loadProfesiones();

    // Nuevo Profesional
    $('#newProfesionalButton').click(() => {
        $('#profesionalForm')[0].reset();
        $('#profesionalId').val('');
        $('#profesionalModal .modal-title').text('Nuevo Profesional');
        new bootstrap.Modal('#profesionalModal').show();
    });

    // Editar Profesional
    $('#profesionalesTable').on('click', '.edit-btn', function () {
        const profesionalId = $(this).data('id');
        axios.get(`/profesionales/${profesionalId}`) // Ruta corregida
            .then(response => {
                // Asignar valores a los campos del formulario
                $('#profesionalId').val(response.data.id);
                $('#nombre').val(response.data.nombre);
                $('#apellido').val(response.data.apellido);
                $('#profesion_id').val(response.data.profesion_id);
                $('#cuil').val(response.data.cuil);
                $('#dni').val(response.data.dni);
                $('#direccion').val(response.data.direccion);
                $('#telefono1').val(response.data.telefono1);
                $('#telefono2').val(response.data.telefono2);
                $('#observaciones').val(response.data.observaciones);

                // Cambiar título del modal a "Editar Profesional"
                $('#profesionalModal .modal-title').text('Editar Profesional');
                
                // Abrir el modal
                new bootstrap.Modal('#profesionalModal').show();
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cargar los datos del profesional',
                });
            });
    });

    // Ver Detalles
    $('#profesionalesTable').on('click', '.view-btn', function () {
        const profesionalId = $(this).data('id');
        axios.get(`/profesionales/${profesionalId}`)
            .then(response => {
                // Llenar el modal con los datos del profesional
                $('#viewNombre').text(response.data.nombre);
                $('#viewApellido').text(response.data.apellido);
                $('#viewProfesion').text(response.data.profesion?.nombre || 'Sin profesión');
                $('#viewCuil').text(response.data.cuil || 'No especificado');
                $('#viewDni').text(response.data.dni || 'No especificado');
                $('#viewDireccion').text(response.data.direccion || 'No especificado');
                $('#viewTelefono1').text(response.data.telefono1 || 'No especificado');
                $('#viewTelefono2').text(response.data.telefono2 || 'No especificado');
                $('#viewObservaciones').text(response.data.observaciones || 'No especificado');
                // Mostrar el modal
                new bootstrap.Modal('#viewProfesionalModal').show();
            })
            .catch(error => {
                if (error.response?.status === 404) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Profesional no encontrado',
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error inesperado al obtener los detalles del profesional',
                    });
                }
            });
    });

    // Eliminar Profesional
    $('#profesionalesTable').on('click', '.delete-btn', function () {
        const profesionalId = $(this).data('id');
        Swal.fire({
            title: '¿Eliminar profesional?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/profesionales/destroy/${profesionalId}`)
                    .then(() => {
                        table.ajax.reload();
                        Swal.fire('¡Eliminado!', '', 'success');
                    });
            }
        });
    });

    // Enviar Formulario
    $('#profesionalForm').submit(function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const profesionalId = $('#profesionalId').val();
        const method = profesionalId ? 'put' : 'post';
        const url = profesionalId ? `/profesionales/update/${profesionalId}` : '/profesionales/store';

        axios[method](url, formData)
            .then(() => {
                table.ajax.reload();
                $('#profesionalModal').modal('hide');
                Swal.fire('¡Éxito!', 'Operación realizada correctamente', 'success');
            })
            .catch(error => {
                if (error.response?.status === 422) {
                    const errors = error.response.data.errors;
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
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Error inesperado',
                    });
                }
            });
    });
});
</script>
@endpush
@endsection