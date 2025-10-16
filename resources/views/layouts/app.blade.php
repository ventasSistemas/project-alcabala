<!--views/layouts/app.blade.php-->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo</title>

    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <!-- Animaciones (opcional, para los efectos de entrada/salida) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        body {
            background-color: #f8f9fc;
            font-family: "Poppins", sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        #wrapper {
            min-height: 100vh;
        }
        #sidebar {
            width: 260px;
            background: linear-gradient(180deg, #0d6efd 0%, #002b6a 100%);
            transition: all 0.3s ease;
        }
        #sidebar .nav-link {
            color: #ffffffcc;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        #sidebar .nav-link:hover, 
        #sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: #fff;
            transform: translateX(4px);
        }
        .navbar {
            background-color: #fff !important;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        #page-content-wrapper {
            flex-grow: 1;
        }

        footer {
            background-color: #fff;
            border-top: 1px solid #dee2e6;
            margin-top: auto; /* Esto asegura que el footer se quede al fondo */
        }
        @media (max-width: 991px) {
            #sidebar {
                position: fixed;
                left: -260px;
                top: 0;
                height: 100%;
                z-index: 1050;
            }
            #sidebar.active {
                left: 0;
            }
        }
    </style>

    <script>
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}',
            clientesBuscarUrl: '{{ route('clientes.buscar') }}'
        };
    </script>

    @stack('styles')
    @livewireStyles
</head>
<body>
    <div class="d-flex" id="wrapper">
        @include('partials.sidebar')

        <div id="page-content-wrapper" class="flex-grow-1">
            @include('partials.header')

            <main class="container-fluid py-4">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Toggle Sidebar -->
    <button id="sidebarToggle" class="btn btn-primary d-lg-none position-fixed" 
        style="top: 15px; left: 15px; z-index: 1100;">
        <i class="fas fa-bars"></i>
    </button>

    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
    @livewireScripts
</body>
</html>
