@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-end gap-2">
            <!-- Contenedor para botones de exportación (izquierda) -->
            <div id="exportButtonsContainer"></div>

            <!-- Botón Nuevo Usuario (derecha) -->
            <button id="newUserButton" class="btn btn-primary">Nuevo</button>
        </div>
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

<!-- Modal -->
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
                        <div id="passwordHelp" class="form-text"></div>
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

<!-- Modal para Ver Detalles del Usuario -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewUserModalLabel">Detalles del Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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

<script>
$('#usersTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('users.indexData') }}",
    columns: [
        { data: 'id' },
        { data: 'name' },
        { data: 'email' },
        {
            data: 'id',
            render: function(data, type, row) {
                return `
                    <button class="btn btn-sm btn-warning edit-btn" data-id="${data}">
                        <i class="fa fa-pencil-alt"></i>
                    </button>
                    <button class="btn btn-sm btn-info view-btn" data-id="${data}">
                        <i class="fa fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="${data}">
                        <i class="fa fa-trash"></i>
                    </button>
                `;
            }
        }
    ],
    language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
    },
    columnDefs: [
        {
            targets: -1,
            width: '120px',
            orderable: false
        }
    ],
    dom: 'Bfrtip',
    buttons: [
        {
            extend: 'excelHtml5',
            text: 'Excel',
            className: 'btn btn-success me-2',  // Agregar margen a la derecha
            container: '#exportButtonsContainer' // Especificar el contenedor
        },
        {
            extend: 'pdfHtml5',
            text: 'PDF',
            className: 'btn btn-danger',
            container: '#exportButtonsContainer' // Especificar el contenedor
        }
    ]
});

$('#newUserButton').click(function() {
    $('#userForm')[0].reset();
    $('#userId').val('');
    $('#userModal .modal-title').text('Nuevo Usuario');
    $('#password').prop('required', true);
    $('#validationErrors').addClass('d-none');
    $('#errorList').empty();
    const modal = new bootstrap.Modal(document.getElementById('userModal'));
    modal.show();
});

$('#usersTable').on('click', '.edit-btn', function() {
    $('#userForm')[0].reset();
    const userId = $(this).data('id');

    axios.get(`/users/${userId}`)
        .then(response => {
            const user = response.data;
            $('#userId').val(user.id);
            $('#name').val(user.name);
            $('#email').val(user.email);
            $('#password').val(user.password || '');
            $('#password').prop('required', false);
            $('#userModal .modal-title').text('Editar Usuario');
            $('#validationErrors').addClass('d-none');
            $('#errorList').empty();
            const modal = new bootstrap.Modal(document.getElementById('userModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error al obtener usuario:', error);
        });
});

$('#userForm').submit(function(e) {
    e.preventDefault();
    const formData = {
        name: $('#name').val(),
        email: $('#email').val(),
        password: $('#password').val(),
    };

    const userId = $('#userId').val();
    const url = userId ? `/users/${userId}` : '/users';
    const method = userId ? 'put' : 'post';

    axios({
        method: method,
        url: url,
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .then(response => {
        $('#usersTable').DataTable().ajax.reload();
        $('#userModal').modal('hide');
        Swal.fire({
            icon: 'success',
            title: 'Usuario guardado exitosamente',
            showConfirmButton: false,
            timer: 1500
        });
    })
    .catch(error => {
        console.error('Error al guardar:', error.response.data);
        $('#validationErrors').removeClass('d-none');
        $('#errorList').empty();
        $.each(error.response.data.errors, function(key, value) {
            $('#errorList').append(`<li>${value}</li>`);
        });
    });
});

$('#usersTable').on('click', '.view-btn', function() {
    const userId = $(this).data('id');
    axios.get(`/users/${userId}`)
        .then(response => {
            const user = response.data;
            $('#viewName').text(user.name);
            $('#viewEmail').text(user.email);
            const modal = new bootstrap.Modal(document.getElementById('viewUserModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error al obtener usuario:', error);
        });
});

$('#usersTable').on('click', '.delete-btn', function() {
    const userId = $(this).data('id');
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'No podrás revertir esta acción.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(`/users/${userId}`)
                .then(response => {
                    $('#usersTable').DataTable().ajax.reload();
                    Swal.fire(
                        'Eliminado!',
                        'El usuario ha sido eliminado.',
                        'success'
                    );
                })
                .catch(error => {
                    console.error('Error al eliminar usuario:', error);
                });
        }
    });
});
</script>
@endpush

@endsection
