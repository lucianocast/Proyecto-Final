<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $users = User::query()
            ->when($q, fn($query) => $query->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"))
            ->orderBy('id', 'desc')
            ->paginate(15);

        // Renderizar la página única de "usuarios-roles" para administrar usuarios
        return view('admin.usuarios_roles', compact('users', 'q'));
    }

    public function create()
    {
        $roles = ['administrador', 'encargado', 'vendedor', 'cliente', 'proveedor'];
        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $data['active'] = true;
        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        $roles = ['administrador', 'encargado', 'vendedor', 'cliente', 'proveedor'];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado.');
    }

    /**
     * Toggle active status (deactivate / reactivate)
     */
    public function destroy(User $user)
    {
        // Prevent an admin from deactivating their own account while logged in
        if (auth()->check() && auth()->id() === $user->id && $user->active) {
            return redirect()->route('admin.users.index')->with('error', 'No puedes desactivar tu propia cuenta mientras estás autenticado.');
        }

        $user->active = ! $user->active;
        $user->save();

        $msg = $user->active ? 'Usuario reactivado.' : 'Usuario desactivado.';

        return redirect()->route('admin.users.index')->with('success', $msg);
    }
}
