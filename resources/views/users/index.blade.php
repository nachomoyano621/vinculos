@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-12">
            <button id="newUserButton" class="btn btn-primary">Nuevo Usuario</button>
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
                    <!-- Sección para mostrar errores de validación -->
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
    <div id="passwordHelp" class="form-text"></div> <!-- Contenedor para el mensaje -->
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
                    <!-- Agrega más campos si es necesario -->
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

@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            targets: -1,  // Columna "Acciones"
            width: '120px', // Ajustar el ancho según lo necesario
            orderable: false  // Evitar que se pueda ordenar por esta columna
        }
    ]
});

    // Nuevo Usuario - Configurar modal limpio
    $('#newUserButton').click(function() {
        $('#userForm')[0].reset();
        $('#userId').val(''); // Limpiar ID oculto
        $('#userModal .modal-title').text('Nuevo Usuario');
        $('#password').prop('required', true); // Hacer requerido el password
        $('#validationErrors').addClass('d-none'); // Ocultar errores al abrir el modal
        $('#errorList').empty(); // Limpiar lista de errores
        const modal = new bootstrap.Modal(document.getElementById('userModal'));
        modal.show();
    });

    $('#usersTable').on('click', '.edit-btn', function() {
    $('#userForm')[0].reset();
    const userId = $(this).data('id');

    axios.get(`/users/${userId}`)
        .then(response => {
            const user = response.data;

            // Asignar valores normales
            $('#userId').val(user.id);
            $('#name').val(user.name);
            $('#email').val(user.email);

            // 1. Mostrar el hash de la contraseña (si existe)
            $('#password').val(user.password || ''); // Asignar el hash al campo



            // 3. Quitar el "required" del campo
            $('#password').prop('required', false);

            // Configurar el modal
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



    // Guardar usuario (funciona para ambos casos)
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
            // SweetAlert para confirmar guardado
            Swal.fire({
                icon: 'success',
                title: 'Usuario guardado exitosamente',
                showConfirmButton: false,
                timer: 1500
            });
        })
        .catch(error => {
            console.error('Error al guardar:', error.response.data);
            // Mostrar errores de validación en la vista
            $('#validationErrors').removeClass('d-none'); // Mostrar el div de errores
            $('#errorList').empty(); // Limpiar la lista de errores
            $.each(error.response.data.errors, function(key, value) {
                $('#errorList').append('<li>' + value[0] + '</li>');
            });

            // Verificar si el backend devuelve un mensaje general
            if (error.response.data.message) {
                // Mostrar mensaje de error general del backend en un SweetAlert
                Swal.fire({
                    icon: 'error',
                    title: 'Error al guardar',
                    text: error.response.data.message,
                });
            } else {
                // SweetAlert para mostrar error en la validación de formulario
                Swal.fire({
                    icon: 'error',
                    title: 'Error en el formulario',
                    text: 'Por favor, verifica los campos y vuelve a intentar.',
                });
            }

            // Ocultar el mensaje de validación cuando se muestra el SweetAlert
            $('#validationErrors').addClass('d-none');
        });
    });

    // Eliminar usuario
    $('#usersTable').on('click', '.delete-btn', function() {
        const userId = $(this).data('id');
        // Usamos SweetAlert en lugar de confirm
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¡No podrás revertir esta acción!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminarlo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/users/${userId}`)
                    .then(() => {
                        $('#usersTable').DataTable().ajax.reload();
                        Swal.fire(
                            'Eliminado!',
                            'El usuario ha sido eliminado.',
                            'success'
                        );
                    })
                    .catch((error) => {
                        // Mostrar error si ocurre durante la eliminación
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al eliminar',
                            text: error.response.data.message || 'Hubo un problema al eliminar el usuario.',
                        });
                    });
            }
        });
    });
    // Ver Usuario - Mostrar datos en el modal
$('#usersTable').on('click', '.view-btn', function() {
    const userId = $(this).data('id');

    // Hacer una solicitud GET para obtener los detalles del usuario
    axios.get(`/users/${userId}`)
        .then(response => {
            const user = response.data;

            // Llenar el modal con los datos del usuario
            $('#viewName').text(user.name);
            $('#viewEmail').text(user.email);
            // Agregar más campos si es necesario

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('viewUserModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error al obtener usuario:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error al cargar detalles',
                text: 'Hubo un problema al obtener los detalles del usuario.',
            });
        });
});
    </script>

@endpush
@endsection
