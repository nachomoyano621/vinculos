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
    </div>
</div>
@endsection


<script>
document.addEventListener("DOMContentLoaded", function () {
    // Cargar el conteo de usuarios
    axios.get("{{ route('users.count') }}")
        .then(function (response) {
            document.getElementById('userCount').innerText = response.data.count;
        })
        .catch(function (error) {
            document.getElementById('userCount').innerText = 'Error';
            console.error('Error al obtener el conteo de usuarios:', error);
        });

    // Cargar el conteo de obras sociales
    axios.get("{{ route('osocial.count') }}")
        .then(function (response) {
            document.getElementById('osocialCount').innerText = response.data.count;
        })
        .catch(function (error) {
            document.getElementById('osocialCount').innerText = 'Error';
            console.error('Error al obtener el conteo de obras sociales:', error);
        });

    // Cargar el conteo de pacientes
    axios.get("{{ route('pacientes.count') }}")
        .then(function (response) {
            document.getElementById('pacienteCount').innerText = response.data.count;
        })
        .catch(function (error) {
            document.getElementById('pacienteCount').innerText = 'Error';
            console.error('Error al obtener el conteo de pacientes:', error);
        });
});
</script>