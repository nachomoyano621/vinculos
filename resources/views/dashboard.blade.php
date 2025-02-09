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
    </div>
</div>
@endsection

@push('scripts')

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Mostrar el spinner de carga en todas las tarjetas
    const spinners = document.querySelectorAll('.fa-spinner');
    spinners.forEach(spinner => spinner.classList.remove('d-none'));

    // URLs de las APIs
    const urls = [
        { id: 'userCount', url: "{{ route('users.count') }}" },
        { id: 'osocialCount', url: "{{ route('osocial.count') }}" },
        { id: 'pacienteCount', url: "{{ route('pacientes.count') }}" },
        { id: 'profesionCount', url: "{{ route('profesiones.count') }}" }
    ];

    // Hacer todas las solicitudes en paralelo
    Promise.all(urls.map(item => 
        axios.get(item.url)
            .then(response => {
                document.getElementById(item.id).innerText = response.data.count;
            })
            .catch(error => {
                document.getElementById(item.id).innerText = 'Error';
                console.error(`Error al obtener el conteo de ${item.id}:`, error);
            })
    )).finally(() => {
        // Ocultar el spinner una vez todas las solicitudes hayan terminado
        spinners.forEach(spinner => spinner.classList.add('d-none'));
    });
});

</script>
@endpush