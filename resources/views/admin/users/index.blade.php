@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-xl font-semibold">Gestión de usuarios</h2>
        <a href="{{ route('admin.users.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded">Nuevo usuario</a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-2 bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif

    <div class="bg-white shadow rounded">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">ID</th>
                    <th class="px-6 py-3 text-left">Nombre</th>
                    <th class="px-6 py-3 text-left">Email</th>
                    <th class="px-6 py-3 text-left">Rol</th>
                    <th class="px-6 py-3 text-left">Activo</th>
                    <th class="px-6 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                <tr>
                    <td class="px-6 py-3">{{ $user->id }}</td>
                    <td class="px-6 py-3">{{ $user->name }}</td>
                    <td class="px-6 py-3">{{ $user->email }}</td>
                    <td class="px-6 py-3">{{ $user->role }}</td>
                    <td class="px-6 py-3">{{ $user->active ? 'Sí' : 'No' }}</td>
                    <td class="px-6 py-3 text-right">
                        <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 mr-2">Editar</a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600" onclick="return confirm('Continuar?')">{{ $user->active ? 'Desactivar' : 'Reactivar' }}</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->withQueryString()->links() }}
    </div>
</div>
@endsection
