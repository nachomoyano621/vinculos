@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Login</div>
            <div class="card-body">
                <form id="loginForm">
                    @csrf
                    <div id="error-alert" class="alert alert-danger d-none"></div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>

                    <!-- Enlace para registrarse alineado al botón de Login -->
                    <div class="d-flex justify-content-between mt-3">
                        <small>¿Desea registrarse? <a href="{{ route('register') }}">Regístrese aquí</a></small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let errorAlert = document.getElementById('error-alert');
        errorAlert.classList.add('d-none');
        errorAlert.innerHTML = '';

        axios.post('{{ route('login') }}', new FormData(this))
            .then(response => {
                if (response.data.success) {
                    window.location.href = response.data.redirect;
                }
            })
            .catch(error => {
                if (error.response) {
                    if (error.response.status === 422) { // Errores de validación
                        let errors = Object.values(error.response.data.errors).flat().join('<br>');
                        errorAlert.innerHTML = errors;
                    } else { // Otros errores (ej: 401)
                        errorAlert.innerHTML = error.response.data.message;
                    }
                    errorAlert.classList.remove('d-none');
                } else {
                    alert('Error de conexión');
                }
            });
    });
</script>


@endsection
