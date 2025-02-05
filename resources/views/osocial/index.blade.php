@extends('layouts.app')

@section('content')
<div class="container">
<h1 class="text-center mb-4">Obras Sociales</h1>
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-end gap-2">
            <div id="exportButtonsContainer"></div>
            <button id="newOSocialButton" class="btn btn-primary">+ Nueva</button>
        </div>
    </div>
    <table class="table table-bordered" id="osocialTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal para Crear/Editar -->
<div class="modal fade" id="osocialModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Obra Social</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="osocialForm">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="osocialId" name="id">
                    <div id="validationErrors" class="alert alert-danger d-none">
                        <ul id="errorList"></ul>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
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
<div class="modal fade" id="viewOSocialModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de la Obra Social</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="viewOSocialDetails">
                    <p><strong>Nombre:</strong> <span id="viewNombre"></span></p>
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
            // Si es un error de validación, mostrar los errores del backend
            showValidationErrors(error.response.data.errors);
        } else {
            // Si no es un error de validación, mostrar el mensaje que venga del backend
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
    // Concatenar todos los errores y mostrarlos en SweetAlert
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

// Función para limpiar errores
function clearValidationErrors() {
    $('#validationErrors').addClass('d-none');
    $('#errorList').empty();
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
}

// Datatable
$(document).ready(function () {
    const table = $('#osocialTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('osocial.data') }}",
        columns: [
            { data: 'id' },
            { data: 'nombre' },
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
                targets: -1, // Aplica la configuración a la última columna (Acciones)
                width: '120px', // Ancho de la columna (ajusta el valor según sea necesario)
                orderable: false, // Para que no sea ordenable
                className: 'text-center', // Alinea los botones en el centro
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

    // Nueva Obra Social
    $('#newOSocialButton').click(() => {
        $('#osocialForm')[0].reset();
        $('#osocialId').val('');
        clearValidationErrors();
        new bootstrap.Modal('#osocialModal').show();
    });

    // Editar Obra Social
    $('#osocialTable').on('click', '.edit-btn', function () {
        const osocialId = $(this).data('id');
        axios.get(`/osocial/show/${osocialId}`)
            .then(response => {
                $('#osocialId').val(response.data.id);
                $('#nombre').val(response.data.nombre);
                clearValidationErrors();
                new bootstrap.Modal('#osocialModal').show();
            });
    });

    // Enviar Formulario
    $('#osocialForm').submit(function (e) {
        e.preventDefault();
        clearValidationErrors();
        const formData = {
            nombre: $('#nombre').val(),
        };
        const osocialId = $('#osocialId').val();
        const method = osocialId ? 'put' : 'post';
        const url = osocialId ? `/osocial/update/${osocialId}` : '/osocial/store';

        axios[method](url, formData)
            .then(() => {
                table.ajax.reload();
                $('#osocialModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Operación realizada correctamente',
                    timer: 1500
                });
            });
    });

    // Ver Detalles
    $('#osocialTable').on('click', '.view-btn', function () {
        const osocialId = $(this).data('id');
        axios.get(`/osocial/show/${osocialId}`)
            .then(response => {
                $('#viewNombre').text(response.data.nombre);
                new bootstrap.Modal('#viewOSocialModal').show();
            });
    });

    // Eliminar Obra Social
    $('#osocialTable').on('click', '.delete-btn', function () {
        const osocialId = $(this).data('id');
        Swal.fire({
            title: '¿Eliminar obra social?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/osocial/destroy/${osocialId}`)
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