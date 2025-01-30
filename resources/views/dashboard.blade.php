@extends('layouts.app')

@section('content')
<div class="container">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <h1 class="text-center mb-4">Vinculos</h1>
<BR><BR>
    <!-- Cards con contadores -->
    <div class="row">
        <!-- Card de Usuarios -->
        <div class="col-md-3 mb-4">
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
            </div>
        </div>
    </div>
    </div>
</div>
@endsection

<script>
document.addEventListener("DOMContentLoaded", function() {
    axios.get("{{ route('users.count') }}")
        .then(function(response) {
            document.getElementById('userCount').innerText = response.data.count;
        })
        .catch(function(error) {
            document.getElementById('userCount').innerText = 'Error';
            console.error('Error al obtener el conteo :', error);
        });
});
</script>
