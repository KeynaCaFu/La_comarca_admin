@extends('layouts.welcome')

@section('title', 'Bienvenido - La Comarca')

@section('content')
<style>
    .welcome-card{
        max-width: 760px;
        margin: clamp(24px, 8vh, 80px) auto;
        padding: clamp(20px, 4vw, 40px);
        background: #ffffffde;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(0,0,0,.08);
        text-align: center;
    }
   
    .logo-icon{ margin-bottom: 6px; }
    .logo-image{
       
        width: min(300px, 70%);
        height: auto;
        filter: drop-shadow(0 6px 16px rgba(0,0,0,.15));
        transition: transform .25s ease;
    }
    .logo-image:hover{ transform: scale(1.02); }
    .welcome-title{
        font-size: clamp(28px, 4vw, 42px);
        
        margin: -52px 0 8px;
        font-weight: 800;
        letter-spacing: .3px;
        color: #1f2937;
    }
    .welcome-subtitle{
        color: #4b5563;
        font-size: clamp(14px, 1.8vw, 18px);
        margin: 0 auto 24px;
        line-height: 1.6;
    }
    .btn-gestionar{
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 20px;
        border-radius: 12px;
        background: linear-gradient(135deg, #485a1a, #0d5e2a);
        color: #ffffff !important;
        text-decoration: none;
        font-weight: 700;
        box-shadow: 0 8px 18px rgba(22, 163, 74, .25);
        transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
    }
    .btn-gestionar:hover{
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(22, 163, 74, .30);
        filter: saturate(1.05);
    }
    .feature-icons{
        display: flex;
        justify-content: center;
        gap: clamp(16px, 3vw, 28px);
        margin-top: 26px;
        color: #6b7280;
    }
    .feature-icons i{
        font-size: clamp(20px, 3vw, 26px);
        padding: 10px;
        border-radius: 10px;
        background: #f3f4f6;
        transition: background .18s ease, color .18s ease;
    }
    .feature-icons i:hover{
        background: #e5f7ed;
        color: #16a34a;
    }
</style>
<div class="welcome-card">
    <div class="logo-icon">
        <img src="{{ asset('images/logo_comarca.png') }}" alt="Logo La Comarca" class="logo-image" loading="lazy">
    </div>
    

    <h1 class="welcome-title">¡Bienvenido!</h1>
    
    <p class="welcome-subtitle">
        Sistema de administración <strong>La Comarca Gastro Park</strong><br>
    </p>
    
    <div style="display:flex; gap:12px; justify-content:center; margin-top:12px; flex-wrap:wrap;">
        @auth
            <a href="{{ route('dashboard') }}" class="btn-gestionar" title="Ir al panel de control">
                <i class="fas fa-tachometer-alt me-2"></i>
                Panel de Control
            </a>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn-gestionar" style="background:linear-gradient(135deg,#7c2d12,#c2410c);" title="Cerrar sesión">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Cerrar Sesión
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn-gestionar" title="Iniciar sesión">
                <i class="fas fa-sign-in-alt me-2"></i>
                Iniciar Sesión
            </a>
        @endauth
    </div>
    
    <div class="feature-icons">
        <i class="fas fa-boxes" title="Insumos"></i>
        <i class="fas fa-truck" title="Proveedores"></i>
        <i class="fas fa-chart-line" title="Reportes"></i>
    </div>
</div>
@endsection