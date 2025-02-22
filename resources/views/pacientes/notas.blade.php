@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Mostrar el nombre del paciente -->
    <h1 class="text-center mb-4">Notas del Paciente: {{ $paciente->nombre }} {{ $paciente->apellido }}</h1>
    <input type="hidden" id="paciente_id" value="{{ $paciente->id }}"> <!-- ID del paciente -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-end gap-2">
            <button id="newNotaButton" class="btn btn-primary">+ Nueva</button>
        </div>
    </div>
    <table class="table table-bordered" id="notasTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Fecha Creación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Modal para Crear/Editar Nota -->
<!-- Modal para Crear/Editar Nota -->
<div class="modal fade" id="notaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Nota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="notaForm">
                @csrf
                <input type="hidden" id="notaId" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" id="titulo" name="titulo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nota</label>
                        <textarea id="nombre" name="nombre" class="form-control" rows="5" required></textarea>
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
<!-- Modal para Ver Detalles -->
<div class="modal fade" id="viewNotaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de la Nota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Título:</strong> <span id="viewTitulo"></span></p>
                <p><strong>Nota:</strong> <span id="viewNombre" style="word-wrap: break-word;"></span></p>
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
    <style>
    /* Estilos para manejar el desbordamiento en los modales */
    .modal-body {
        max-height: 60vh; /* Altura máxima del cuerpo del modal */
        overflow-y: auto; /* Habilitar desplazamiento vertical si es necesario */
    }

    /* Limitar la altura de los campos de texto largos */
    textarea.form-control {
        max-height: 150px; /* Altura máxima para campos de texto */
        overflow-y: auto; /* Habilitar desplazamiento vertical dentro del campo */
    }

    /* Ajustar el ancho del modal */
    .modal-dialog {
        max-width: 800px; /* Ancho máximo del modal */
    }

    /* Evitar que el texto se escape del modal */
    .modal-content {
        word-wrap: break-word; /* Romper palabras largas para evitar desbordamiento */
    }
</style>

@endpush

@push('scripts')
<script>
$(document).ready(function () {
    const pacienteId = $('#paciente_id').val(); // Obtiene el ID del paciente
    console.log("Paciente ID:", pacienteId); // ✅ Verifica si el ID es correcto
    if (!pacienteId) {
        console.error("Error: paciente_id no está definido.");
        return;
    }
    let table = $('#notasTable').DataTable({
    processing: true,
    serverSide: true, // Habilitamos el modo servidor
    ajax: {
        url: `/pacientes/${pacienteId}/notas/data`, // Ruta para obtener las notas
        type: 'GET',
        error: function (xhr, status, error) {
            console.error("Error en DataTable:", xhr.responseText); // Muestra errores en la consola
        }
    },
    columns: [
        { data: 'id' }, // ID
        { data: 'titulo' }, // Título
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
            targets: -1, // Última columna (Acciones)
            width: '160px', // Ancho fijo para la columna Acciones
            orderable: false, // Deshabilitar ordenación en esta columna
            className: 'text-center', // Alinear el contenido al centro
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
        ],
    order: [[2, 'desc']] // Ordenar por la columna de fecha de creación (índice 2) de forma descendente
});
    // Botón para abrir el modal de Nueva Nota
    $('#newNotaButton').click(() => {
        $('#notaForm')[0].reset();
        $('#notaId').val('');
        new bootstrap.Modal('#notaModal').show();
    });

    // Guardar/Actualizar Nota
    $('#notaForm').submit(function (e) {
        e.preventDefault();
        const formData = {
            paciente_id: $('#paciente_id').val(),
            titulo: $('#titulo').val(), // Nuevo campo
            nombre: $('#nombre').val(),
        };
        const notaId = $('#notaId').val();
        const method = notaId ? 'put' : 'post';
        const url = notaId ? `/notas/${notaId}` : '/notas/store';

        axios[method](url, formData)
            .then(() => {
                table.ajax.reload();
                $('#notaModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Nota guardada correctamente',
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
                        text: 'Ocurrió un error al guardar la nota',
                    });
                }
            });
    });

    // Ver Detalles de una Nota
    $('#notasTable').on('click', '.view-btn', function () {
        const notaId = $(this).data('id');
        axios.get(`/notas/${notaId}`)
            .then(response => {
                $('#viewTitulo').text(response.data.titulo); // Nuevo campo
                $('#viewNombre').text(response.data.nombre);
                const date = new Date(response.data.created_at);
                const formattedDate = `${String(date.getDate()).padStart(2, '0')}/${String(date.getMonth() + 1).padStart(2, '0')}/${date.getFullYear()} ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
                $('#viewCreatedAt').text(formattedDate);
                new bootstrap.Modal('#viewNotaModal').show();
            });
    });

    // Editar Nota
    $('#notasTable').on('click', '.edit-btn', function () {
        const notaId = $(this).data('id');
        axios.get(`/notas/${notaId}`)
            .then(response => {
                $('#notaId').val(response.data.id);
                $('#titulo').val(response.data.titulo); // Nuevo campo
                $('#nombre').val(response.data.nombre);
                new bootstrap.Modal('#notaModal').show();
            });
    });

    // Eliminar Nota
    $('#notasTable').on('click', '.delete-btn', function () {
        const notaId = $(this).data('id');
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/notas/${notaId}`)
                    .then(() => {
                        table.ajax.reload();
                        Swal.fire('Eliminado', 'La nota ha sido eliminada.', 'success');
                    });
            }
        });
    });
});
</script>
@endpush