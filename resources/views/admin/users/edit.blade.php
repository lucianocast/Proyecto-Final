@extends('layouts.encargado')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h2 class="text-lg font-semibold mb-4">Editar usuario</h2>

    @if($errors->any())
        <div class="mb-4 text-red-700">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-2">
            <label class="block">Nombre</label>
            <input name="name" value="{{ old('name', $user->name) }}" class="w-full border rounded px-2 py-1" />
        </div>
        <div class="mb-2">
            <label class="block">Email</label>
            <input name="email" value="{{ old('email', $user->email) }}" class="w-full border rounded px-2 py-1" />
        </div>
        <div class="mb-2">
            <label class="block">Rol</label>
            <select name="role" class="w-full border rounded px-2 py-1">
                @foreach($roles as $r)
                    <option value="{{ $r }}" {{ old('role', $user->role)===$r? 'selected':'' }}>{{ $r }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-2">
            <label class="block">Password (dejar vacío para no cambiar)</label>
            <input name="password" type="password" class="w-full border rounded px-2 py-1" />
        </div>

        <div class="mt-4">
            <button class="bg-blue-600 text-white px-3 py-1 rounded">Guardar</button>
            <a href="{{ route('admin.users.index') }}" class="ml-2 text-gray-600">Cancelar</a>
        </div>
    </form>
</div>
@endsection
