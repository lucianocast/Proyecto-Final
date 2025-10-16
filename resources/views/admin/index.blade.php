@extends('layouts.encargado')

@section('title', 'Admin')
@section('heading', 'Panel Administrador')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="p-4 bg-white rounded shadow">Gestionar usuarios y roles<br><a href="{{ route('admin.usuarios_roles') }}">Ir</a></div>
        <div class="p-4 bg-white rounded shadow">Configurar Sistema<br><a href="{{ route('admin.sistema') }}">Ir</a></div>
        <div class="p-4 bg-white rounded shadow">Notificaciones y Alertas<br><a href="{{ route('admin.notificaciones') }}">Ir</a></div>
        <div class="p-4 bg-white rounded shadow">Reportes y Análisis<br><a href="{{ route('admin.reportes') }}">Ir</a></div>
        <div class="p-4 bg-white rounded shadow">Auditoría del Sistema<br><a href="{{ route('admin.auditoria') }}">Ir</a></div>
    </div>
@endsection
