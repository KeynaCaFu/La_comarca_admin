<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'La Comarca - Admin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- CSS Personalizado La Comarca -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fixes.css') }}" rel="stylesheet">
    <link href="{{ asset('css/modals.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <!-- Container principal con diseño La Comarca -->
    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-utensils"></i> La Comarca</h3>
            </div>
            <div class="sidebar-menu">
                <ul>
                    <li>
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('insumos.index') }}" class="{{ request()->routeIs('insumos*') ? 'active' : '' }}">
                            <i class="fas fa-boxes"></i> Insumos
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('proveedores.index') }}" class="{{ request()->routeIs('proveedores*') ? 'active' : '' }}">
                            <i class="fas fa-truck"></i> Proveedores
                        </a>
                    </li>
                    <li class="mt-3">
                        <a href="{{ route('welcome') }}" class="text-danger">
                            <i class="fas fa-sign-out-alt"></i> Salir
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Contenido principal -->
        <main class="main-content">
            <!-- Header -->
            <div class="header">
                <h1>@yield('title', 'Dashboard')</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle fa-2x"></i>
                    <span>Administrador</span>
                </div>
            </div>

            <!-- Alertas de Bootstrap/Laravel -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Contenido específico de cada página -->
            <div class="fade-in">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>