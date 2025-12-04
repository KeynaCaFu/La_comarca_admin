<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                <a href="{{ route('dashboard') }}" class="brand text-decoration-none">
                    <span class="brand-text">La Comarca</span>
                </a>
            </div>
            <div class="sidebar-menu">
                <ul>
                    @php $mode = session('admin_mode', 'local'); @endphp

                    @if($mode === 'global')
                        <li>
                            <a href="{{ route('eventos.index') }}" class="{{ request()->routeIs('eventos*') ? 'active' : '' }}">
                                <i class="fas fa-calendar-days"></i> Eventos
                            </a>
                        </li>
                        <li class="mt-3">
                            <a href="{{ route('welcome') }}" class="text-danger">
                                <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('supplies.index') }}" class="{{ request()->routeIs('supplies*') ? 'active' : '' }}">
                                <i class="fas fa-boxes"></i> Insumos
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers*') ? 'active' : '' }}">
                                <i class="fas fa-truck"></i> Proveedores
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products*') ? 'active' : '' }}">
                                <i class="fas fa-box-open"></i> Productos
                            </a>
                        </li>
                        <li class="mt-3">
                            <a href="{{ route('welcome') }}" class="text-danger">
                                <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            
            <!-- Usuario administrador al final del sidebar -->
            <div class="sidebar-footer">
                <div class="admin-info">
                    <i class="fas fa-user-circle fa-2x"></i>
                    <span>Administrador</span>
                </div>
            </div>
        </nav>

        <!-- Contenido principal -->
        <main class="main-content">
            <!-- Header -->
            <div class="header">
                <h1>@yield('title', 'Dashboard')</h1>
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