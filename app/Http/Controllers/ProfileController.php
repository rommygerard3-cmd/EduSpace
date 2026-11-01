<?php

namespace App\Http\Controllers;

use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_if($user->isStudent(), 403, 'Les informations du profil élève sont en lecture seule.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $old = $user->only(['name', 'email', 'phone']);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('photos', 'public');
        }

        unset($validated['photo']);
        $user->update($validated);
        $this->auditLogger->log('profile.updated', $user, $old, $user->only(['name', 'email', 'phone']));

        return back()->with('success', 'Profil mis à jour.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user = $request->user();
        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ])->save();

        $this->auditLogger->log('profile.password_changed', $user);

        return back()->with('success', 'Mot de passe modifié.');
    }
}
