@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">PROFESIONES</h1>
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-end gap-2">
            <button id="newProfesionButton" class="btn btn-primary">+ Nueva</button>
        </div>
    </div>
    <table class="table table-bordered" id="profesionesTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Fecha Creación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Modal para Crear/Editar Profesión -->
<div class="modal fade" id="profesionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">+ Nueva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="profesionForm">
                @csrf
                <input type="hidden" id="profesionId" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <textarea id="nombre" name="nombre" class="form-control" rows="3" required></textarea>
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
<div class="modal fade" id="viewProfesionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de la Profesión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Nombre:</strong> <span id="viewNombre"></span></p>
                <p><strong>Fecha Creación:</strong> <span id="viewCreatedAt"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .invalid-feedback { display: block; color: #dc3545; }
    .is-invalid { border-color: #dc3545 !important; }
</style>
@endpush

@push('scripts')


<script>
$(document).ready(function () {
    let table = $('#profesionesTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: "{{ route('profesiones.indexData') }}", // Ruta para obtener los datos
        columns: [
            { data: 'id' }, // ID
            { data: 'nombre' }, // Nombre
            { 
                data: 'created_at',
                render: function (data) {
                    // Formatear la fecha como DD/MM/AAAA HH:MM
                    const date = new Date(data);
                    const formattedDate = `${String(date.getDate()).padStart(2, '0')}/${String(date.getMonth() + 1).padStart(2, '0')}/${date.getFullYear()} ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
                    return formattedDate;
                }
            },
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

    // Botón para abrir el modal de Nueva Profesión
    $('#newProfesionButton').click(() => {
        $('#profesionForm')[0].reset();
        $('#profesionId').val('');
        new bootstrap.Modal('#profesionModal').show();
    });

    // Guardar/Actualizar Profesión
    $('#profesionForm').submit(function (e) {
        e.preventDefault();
        const formData = {
            nombre: $('#nombre').val(),
        };
        const profesionId = $('#profesionId').val();
        const method = profesionId ? 'put' : 'post';
        const url = profesionId ? `/profesiones/${profesionId}` : '/profesiones';

        axios[method](url, formData)
            .then(() => {
                table.ajax.reload();
                $('#profesionModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Profesión guardada correctamente',
                    timer: 1500
                });
            })
            .catch(error => {
                if (error.response && error.response.status === 422) {
                    const errors = Object.values(error.response.data.errors).flat();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de validación',
                        html: errors.join('<br>'),
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al guardar la profesión.',
                    });
                }
            });
    });

 // Ver Detalles de una Profesión
$('#profesionesTable').on('click', '.view-btn', function () {
    const profesionId = $(this).data('id'); // Obtiene el ID de la profesión
    axios.get(`/profesiones/${profesionId}`) // Llama a la ruta GET
        .then(response => {
            const profesion = response.data; // Datos de la profesión
            $('#viewNombre').text(profesion.nombre); // Muestra el nombre
            const date = new Date(profesion.created_at); // Formatea la fecha
            const formattedDate = `${String(date.getDate()).padStart(2, '0')}/${String(date.getMonth() + 1).padStart(2, '0')}/${date.getFullYear()} ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
            $('#viewCreatedAt').text(formattedDate); // Muestra la fecha formateada
            new bootstrap.Modal('#viewProfesionModal').show(); // Abre el modal
        })
        .catch(error => {
            console.error("Error al cargar los detalles:", error); // Depura errores en la consola
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al cargar los detalles de la profesión.',
            });
        });
});

    // Editar Profesión
    $('#profesionesTable').on('click', '.edit-btn', function () {
        const profesionId = $(this).data('id');
        axios.get(`/profesiones/${profesionId}`)
            .then(response => {
                $('#profesionId').val(response.data.id);
                $('#nombre').val(response.data.nombre);
                new bootstrap.Modal('#profesionModal').show();
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al cargar los datos de la profesión.',
                });
            });
    });

  
   // Eliminar Profesión
   $('#profesionesTable').on('click', '.delete-btn', function () {
        const profesionId = $(this).data('id'); // Obtiene el ID de la profesión
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/profesiones/${profesionId}`) // Llama a la ruta DELETE
                    .then(response => {
                        if (response.data.success) {
                            table.ajax.reload(); // Recarga la tabla
                            Swal.fire('Eliminado', 'La profesión ha sido eliminada.', 'success');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo eliminar la profesión.',
                            });
                        }
                    })
                    .catch(error => {
                        console.error("Error al eliminar:", error); // Depura errores en la consola
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al eliminar la profesión.',
                        });
                    });
            }
        });
    });
});
</script>
@endpush