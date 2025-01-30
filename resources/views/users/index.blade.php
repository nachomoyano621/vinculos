@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-end gap-2">
            <div id="exportButtonsContainer"></div>
            <button id="newUserButton" class="btn btn-primary">Nuevo</button>
        </div>
    </div>

    <table class="table table-bordered" id="usersTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal para Crear/Editar -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="userId" name="id">
                    <div id="validationErrors" class="alert alert-danger d-none">
                        <ul id="errorList"></ul>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password">
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
<div class="modal fade" id="viewUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="viewUserDetails">
                    <p><strong>Nombre:</strong> <span id="viewName"></span></p>
                    <p><strong>Correo:</strong> <span id="viewEmail"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.9/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">
<style>
    .invalid-feedback { display: block; color: #dc3545; }
    .is-invalid { border-color: #dc3545 !important; }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
// Configuración global de Axios
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 422) {
            showValidationErrors(error.response.data.errors);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.response?.data?.message || 'Error inesperado',
            });
        }
        return Promise.reject(error);
    }
);

// Función para mostrar errores de validación
function showValidationErrors(errors) {
    $('#validationErrors').removeClass('d-none');
    $('#errorList').empty();

    Object.entries(errors).forEach(([field, messages]) => {
        $(`#${field}`).addClass('is-invalid');
        $(`#${field}`).after(`<div class="invalid-feedback">${messages[0]}</div>`);
    });
}

// Función para limpiar errores
function clearValidationErrors() {
    $('#validationErrors').addClass('d-none');
    $('#errorList').empty();
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
}

// DataTable
$(document).ready(function() {
    const table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('users.indexData') }}",
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            {
                data: 'id',
                render: function(data) {
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

    // Nuevo Usuario
    $('#newUserButton').click(() => {
        $('#userForm')[0].reset();
        $('#userId').val('');
        $('#password').prop('required', true);
        clearValidationErrors();
        new bootstrap.Modal('#userModal').show();
    });

    // Editar Usuario
    $('#usersTable').on('click', '.edit-btn', function() {
        const userId = $(this).data('id');
        axios.get(`/users/${userId}`)
            .then(response => {
                $('#userId').val(response.data.id);
                $('#name').val(response.data.name);
                $('#email').val(response.data.email);
                $('#password').prop('required', false);
                clearValidationErrors();
                new bootstrap.Modal('#userModal').show();
            });
    });

    // Enviar Formulario
    $('#userForm').submit(function(e) {
        e.preventDefault();
        clearValidationErrors();

        const formData = {
            name: $('#name').val(),
            email: $('#email').val(),
            password: $('#password').val(),
        };

        const userId = $('#userId').val();
        const method = userId ? 'put' : 'post';
        const url = userId ? `/users/${userId}` : '/users';

        axios[method](url, formData)
            .then(() => {
                table.ajax.reload();
                $('#userModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Operación realizada correctamente',
                    timer: 1500
                });
            });
    });

    // Ver Detalles
    $('#usersTable').on('click', '.view-btn', function() {
        const userId = $(this).data('id');
        axios.get(`/users/${userId}`)
            .then(response => {
                $('#viewName').text(response.data.name);
                $('#viewEmail').text(response.data.email);
                new bootstrap.Modal('#viewUserModal').show();
            });
    });

    // Eliminar Usuario
    $('#usersTable').on('click', '.delete-btn', function() {
        const userId = $(this).data('id');
        Swal.fire({
            title: '¿Eliminar usuario?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/users/${userId}`)
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
