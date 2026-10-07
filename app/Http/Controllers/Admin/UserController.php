<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('club')
            ->when($request->query('q'), fn ($q, $s) => $q->where(fn ($q) => $q->whereLike('name', "%$s%")->orWhereLike('email', "%$s%")))
            ->orderByRaw("case role when 'super_admin' then 0 when 'club_admin' then 1 else 2 end")
            ->orderBy('name')
            ->paginate(25)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.form', ['user' => new User(['role' => 'club_admin', 'active' => true]), 'clubs' => $this->clubs()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Compte créé. Communiquez le mot de passe à son titulaire.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', ['user' => $user, 'clubs' => $this->clubs()]);
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);

        // Empêche de se retirer soi-même les droits ou de se désactiver.
        if ($user->is($request->user())) {
            $data['role'] = 'super_admin';
            $data['active'] = true;
        }
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Compte mis à jour.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->is($request->user()), 422, 'Vous ne pouvez pas supprimer votre propre compte.');
        $user->delete();

        return back()->with('success', 'Compte supprimé.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user)],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'club_id' => ['nullable', 'required_if:role,club_admin', Rule::exists('clubs', 'id')],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::min(10)->letters()->numbers()],
        ], [
            'club_id.required_if' => 'Un administrateur de club doit être rattaché à un club.',
        ]);

        $data['active'] = $request->boolean('active');
        if ($data['role'] === 'super_admin') {
            $data['club_id'] = null;
        }

        return $data;
    }

    private function clubs()
    {
        return Club::orderBy('name')->get(['id', 'name']);
    }
}
