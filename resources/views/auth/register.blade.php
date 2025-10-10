@extends('layouts.auth')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-sm">
        <h2 class="text-2xl font-semibold text-center mb-4">Registro de Usuario</h2>

        @if ($errors->any())
            <div class="bg-red-100 text-red-600 p-2 rounded mb-3 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700">Nombre</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full border-gray-300 rounded p-2 mt-1 focus:ring focus:ring-blue-300">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full border-gray-300 rounded p-2 mt-1 focus:ring focus:ring-blue-300">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Contraseña</label>
                <input type="password" name="password" required
                    class="w-full border-gray-300 rounded p-2 mt-1 focus:ring focus:ring-blue-300">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Confirmar contraseña</label>
                <input type="password" name="password_confirmation" required
                    class="w-full border-gray-300 rounded p-2 mt-1 focus:ring focus:ring-blue-300">
            </div>

            <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded transition">
                Registrar
            </button>

            <p class="text-center text-sm mt-4">
                ¿Ya tienes una cuenta?
                <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Inicia sesión</a>
            </p>
        </form>
    </div>
</div>
@endsection