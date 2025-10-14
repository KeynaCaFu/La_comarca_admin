@extends('layouts.welcome')

@section('title', 'Bienvenido - La Comarca')

@section('content')
<div class="welcome-card">
    <div class="logo-icon">
        <i class="fas fa-utensils"></i>
    </div>
    
    <h1 class="welcome-title">¡Bienvenido!</h1>
    
    <p class="welcome-subtitle">
        Sistema de administración <strong>La Comarca</strong><br>
        Gestiona tu restaurante de manera eficiente y sencilla
    </p>
    
    <a href="{{ route('dashboard') }}" class="btn-gestionar">
        <i class="fas fa-home me-2"></i>
        Gestionar mi Local
    </a>
    
    <div class="feature-icons">
        <i class="fas fa-boxes" title="Insumos"></i>
        <i class="fas fa-truck" title="Proveedores"></i>
        <i class="fas fa-chart-line" title="Reportes"></i>
    </div>
</div>
@endsection