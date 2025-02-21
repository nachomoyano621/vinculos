@extends('layouts.app')

@section('content')
<div class="container">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <h1 class="text-center mb-4">Vinculos</h1>
    <br><br>
    <!-- Cards con contadores -->
    <div class="row">
        <!-- Card de Usuarios -->
        <div class="col-md-3 mb-4">
            <a href="{{ route('users.index') }}" style="text-decoration: none;">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h4 class="card-title mb-3">USUARIOS</h4>
                        <div class="count-wrapper text-center">
                            <span class="count-number fs-1" id="userCount" style="letter-spacing: 2px;">
                                <i class="fa fa-spinner fa-spin fa-1x"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <!-- Card de Obras Sociales -->
        <div class="col-md-3 mb-4">
            <a href="{{ route('osocial.index') }}" style="text-decoration: none;">
                <div class="card text-white bg-success h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h4 class="card-title mb-3">OBRAS SOCIALES</h4>
                        <div class="count-wrapper text-center">
                            <span class="count-number fs-1" id="osocialCount" style="letter-spacing: 2px;">
                                <i class="fa fa-spinner fa-spin fa-1x"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <!-- Card de Pacientes -->
        <div class="col-md-3 mb-4">
            <a href="{{ route('pacientes.index') }}" style="text-decoration: none;">
                <div class="card text-white bg-danger h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h4 class="card-title mb-3">PACIENTES</h4>
                        <div class="count-wrapper text-center">
                            <span class="count-number fs-1" id="pacienteCount" style="letter-spacing: 2px;">
                                <i class="fa fa-spinner fa-spin fa-1x"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <!-- Card de Profesiones -->
        <div class="col-md-3 mb-4">
            <a href="{{ route('profesiones.index') }}" style="text-decoration: none;">
                <div class="card text-white bg-info h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h4 class="card-title mb-3">PROFESIONES</h4>
                        <div class="count-wrapper text-center">
                            <span class="count-number fs-1" id="profesionCount" style="letter-spacing: 2px;">
                                <i class="fa fa-spinner fa-spin fa-1x"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <!-- Card de Profesionales -->
        <div class="col-md-3 mb-4">
            <a href="{{ route('profesionales.index') }}" style="text-decoration: none;">
                <div class="card text-white bg-warning h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h4 class="card-title mb-3">PROFESIONALES</h4>
                        <div class="count-wrapper text-center">
                            <span class="count-number fs-1" id="profesionalesCount" style="letter-spacing: 2px;">
                                <i class="fa fa-spinner fa-spin fa-1x"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Realizar una única solicitud para obtener todos los conteos
    axios.get("{{ route('counts') }}")
        .then(function (response) {
            // Actualizar los elementos con los datos recibidos
            document.getElementById('userCount').innerText = response.data.userCount;
            document.getElementById('osocialCount').innerText = response.data.osocialCount;
            document.getElementById('pacienteCount').innerText = response.data.pacienteCount;
            document.getElementById('profesionCount').innerText = response.data.profesionCount;
            document.getElementById('profesionalesCount').innerText = response.data.profesionalesCount;
        })
        .catch(function (error) {
            // Manejo de error en caso de que falle la solicitud
            document.getElementById('userCount').innerText = 'Error';
            document.getElementById('osocialCount').innerText = 'Error';
            document.getElementById('pacienteCount').innerText = 'Error';
            document.getElementById('profesionCount').innerText = 'Error';
            document.getElementById('profesionalesCount').innerText = 'Error';
            console.error('Error al obtener los conteos:', error);
        });
});
</script>
@endpush
