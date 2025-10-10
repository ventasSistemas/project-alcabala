@extends('layouts.auth')

@section('content')
<div class="container-fluid vh-100 d-flex flex-column flex-md-row p-0">
    <!-- LADO IZQUIERDO - Imagen con texto superpuesto -->
    <div class="col-md-6 position-relative d-none d-md-block p-0">
        <img src="{{ asset('images/login-bg.jpg') }}" alt="Imagen de fondo" class="w-100 h-100 object-fit-cover">

        <div class="position-absolute top-50 start-50 translate-middle text-center text-white px-4" 
            style="background: rgba(0,0,0,0.4); border-radius: 15px; padding: 25px;">
            <h1 class="fw-bold mb-3">Bienvenido a <br> <span class="text-warning">Alcabala</span></h1>
            <p class="lead mb-0">Gestiona contratos, pagos y clientes de manera rápida y segura.</p>
        </div>
    </div>

    <!-- LADO DERECHO - Formulario -->
    <div class="col-md-6 d-flex align-items-center justify-content-center bg-light p-4">
        <div class="card shadow-lg border-0 p-4 w-100" style="max-width: 400px;">
            <div class="text-center mb-4">
                <h4 class="fw-bold text-primary">Iniciar Sesión</h4>
                <p class="text-muted small">Accede con tus credenciales</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger small py-2">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="form-control" placeholder="ejemplo@correo.com">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Contraseña</label>
                    <input type="password" name="password" id="password" required
                        class="form-control" placeholder="••••••••">
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary btn-lg">Ingresar</button>
                </div>

                <p class="text-center small mb-0">
                    ¿No tienes cuenta?
                    <a href="{{ route('register') }}" class="text-decoration-none text-primary fw-semibold">Regístrate</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection