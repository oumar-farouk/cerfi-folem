<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->orderBy('name')->get();
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:users,name'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required'],
        ]);

        $passwordHashed = bcrypt($request->input('password'));

        $user = User::create($request->only('name', 'email') + ['password' => $passwordHashed]);
        $user->syncRoles([$request->input('role')]);

        return back()->with('success', 'Utilisateur ajouté.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $donnees = $request->validate([
            'name' => ['required', 'string', 'max:150', Rule::unique('users', 'name')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::exists('roles', 'name')->where('guard_name', 'web')],
        ]);

        // On ne peut pas retirer le rôle super-admin au dernier super-admin.
        if ($user->hasRole('super-admin')
            && $donnees['role'] !== 'super-admin'
            && User::role('super-admin')->count() <= 1) {
            return back()->withErrors(['role' => 'Il doit rester au moins un super administrateur.']);
        }

        $attributs = [
            'name' => $donnees['name'],
            'email' => $donnees['email'],
        ];

        // Mot de passe laissé vide = inchangé.
        if (filled($donnees['password'] ?? null)) {
            $attributs['password'] = Hash::make($donnees['password']);
        }

        $user->update($attributs);
        $user->syncRoles([$donnees['role']]);

        return back()->with('success', 'Utilisateur mis à jour.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return back()->with('success', 'Utilisateur supprimé.');
    }
}
