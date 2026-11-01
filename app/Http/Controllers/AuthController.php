<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ActivateAccountRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\ActivationService;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private readonly ActivationService $activationService,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->to(Auth::user()->dashboardRoute());
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $key = 'login:'.strtolower($request->string('matricule')).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'matricule' => 'Trop de tentatives. Réessayez dans '.RateLimiter::availableIn($key).' secondes.',
            ]);
        }

        $user = User::where('matricule', $request->string('matricule'))->first();

        if (! $user) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'matricule' => 'Matricule ou mot de passe incorrect.',
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'matricule' => 'Ce compte est désactivé. Contactez l’administration.',
            ]);
        }

        if ($user->activation_code_hash && ! $user->activated_at) {
            return redirect()->route('activate.show', ['matricule' => $user->matricule])
                ->with('warning', 'Ce compte n’est pas encore activé. Utilisez le code fourni par l’administration.');
        }

        if (! Auth::attempt($request->only('matricule', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'matricule' => 'Matricule ou mot de passe incorrect.',
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        $user = Auth::user();
        $user->forceFill(['last_login_at' => now()])->save();
        $this->auditLogger->log('auth.login', $user);

        if ($user->must_change_password) {
            return redirect()->route('profile.edit')->with('warning', 'Définissez votre mot de passe personnel avant de continuer.');
        }

        return redirect()->intended($user->dashboardRoute());
    }

    public function showActivation(Request $request): View
    {
        return view('auth.activate', [
            'matricule' => $request->query('matricule', old('matricule')),
        ]);
    }

    public function activate(ActivateAccountRequest $request): RedirectResponse
    {
        $user = User::where('matricule', $request->string('matricule'))->first();

        if (! $user) {
            throw ValidationException::withMessages(['matricule' => 'Aucun compte ne correspond à ce matricule.']);
        }

        if ($user->activated_at) {
            throw ValidationException::withMessages(['matricule' => 'Ce compte est déjà activé. Connectez-vous avec votre mot de passe.']);
        }

        if (! $this->activationService->activate($user, $request->string('activation_code'), $request->string('password'))) {
            throw ValidationException::withMessages(['activation_code' => 'Code d’activation invalide ou expiré.']);
        }

        Auth::login($user);
        $request->session()->regenerate();
        $user->forceFill(['last_login_at' => now()])->save();
        $this->auditLogger->log('auth.activated', $user);

        return redirect()->route('dashboard')->with('success', 'Votre compte est activé. Bienvenue sur EduSpace.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->auditLogger->log('auth.logout', $request->user());
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
