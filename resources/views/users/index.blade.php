@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-end gap-2">
            <div id="exportButtonsContainer"></div>
            <button id="newUserButton" class="btn btn-primary">+ Nuevo</button>
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
    // Concatenar todos los errores y mostrarlo en SweetAlert
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

//Datatable
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
