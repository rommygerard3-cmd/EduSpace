<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\ActivationService;
use App\Services\AuditLogger;
use App\Services\MatriculeGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private readonly ActivationService $activationService,
        private readonly MatriculeGenerator $matriculeGenerator,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function index(Request $request): View
    {
        $users = User::query()
            ->with('roles')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(fn ($inner) => $inner->where('name', 'like', $term)
                    ->orWhere('matricule', 'like', $term)
                    ->orWhere('email', 'like', $term));
            })
            ->when($request->filled('role'), fn ($query) => $query->whereHas('roles', fn ($role) => $role->where('name', $request->string('role'))))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => Role::orderBy('label')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => Role::orderBy('label')->get(),
            'user' => new User,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'matricule' => ['nullable', 'string', 'max:50', 'unique:users,matricule'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $matricule = $validated['matricule'] ?: $this->matriculeGenerator->forUser($this->prefixForRole($validated['role']));

        $user = User::create([
            'school_id' => $request->user()->school_id,
            'name' => $validated['name'],
            'matricule' => $matricule,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make(Str::password(32)),
            'is_active' => true,
        ]);

        $user->assignRole($validated['role']);
        $code = $this->activationService->prepare($user);

        $this->auditLogger->log('user.created', $user, [], ['matricule' => $user->matricule, 'role' => $validated['role']]);

        return redirect()->route('admin.users.index')
            ->with('success', "Compte créé pour {$user->name}. Matricule : {$user->matricule}. Code d’activation à usage unique : {$code}. Transmettez ces informations à l’utilisateur.")
            ->with('activation_code', $code);
    }

    public function show(User $user): View
    {
        $user->load(['roles', 'teacher', 'student.currentEnrollment.schoolClass', 'auditLogs' => fn ($query) => $query->latest('created_at')->limit(20)]);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user->load('roles'),
            'roles' => Role::orderBy('label')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'exists:roles,name'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $old = $user->only(['name', 'email', 'phone', 'is_active']);

        if ($user->id === $request->user()->id && ! $request->boolean('is_active')) {
            return back()->withErrors(['is_active' => 'Vous ne pouvez pas désactiver votre propre compte.']);
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active', $user->is_active),
        ]);

        $user->roles()->sync([Role::where('name', $validated['role'])->value('id')]);
        $this->auditLogger->log('user.updated', $user, $old, $user->only(['name', 'email', 'phone', 'is_active']));

        return redirect()->route('admin.users.index')->with('success', 'Compte mis à jour.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'Vous ne pouvez pas désactiver votre propre compte.']);
        }

        if ($user->hasRole(Role::ADMINISTRATEUR_GENERAL) && User::whereHas('roles', fn ($query) => $query->where('name', Role::ADMINISTRATEUR_GENERAL))->where('is_active', true)->count() <= 1) {
            return back()->withErrors(['user' => 'Le dernier administrateur général ne peut pas être désactivé.']);
        }

        $user->update(['is_active' => false]);
        $this->auditLogger->log('user.deactivated', $user, ['is_active' => true], ['is_active' => false]);

        return redirect()->route('admin.users.index')->with('success', 'Compte désactivé. Son historique est conservé.');
    }

    public function activate(User $user): RedirectResponse
    {
        if ($user->activated_at) {
            return back()->withErrors(['user' => 'Ce compte est déjà activé. Utilisez la réinitialisation de mot de passe.']);
        }

        $code = $this->activationService->prepare($user);
        $this->auditLogger->log('user.activation_code.created', $user);

        return back()->with('success', "Nouveau code d’activation pour {$user->matricule} : {$code}")->with('activation_code', $code);
    }

    public function resetPassword(User $user): RedirectResponse
    {
        $code = $this->activationService->prepare($user);
        $this->auditLogger->log('user.password_reset_requested', $user);

        return back()->with('success', "Procédure de récupération générée pour {$user->matricule}. Code d’activation à usage unique : {$code}")->with('activation_code', $code);
    }

    public function toggle(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'Vous ne pouvez pas modifier l’état de votre propre compte.']);
        }

        $user->update(['is_active' => ! $user->is_active]);
        $this->auditLogger->log('user.toggled', $user, ['is_active' => ! $user->is_active], ['is_active' => $user->is_active]);

        return back()->with('success', $user->is_active ? 'Compte réactivé.' : 'Compte désactivé.');
    }

    private function prefixForRole(string $role): string
    {
        return match ($role) {
            Role::ELEVE => 'MAT',
            Role::ENSEIGNANT => 'ENS',
            Role::DIRECTION => 'DIR',
            Role::PREFET => 'PRF',
            Role::DIRECTEUR_DISCIPLINE => 'DIS',
            Role::SECRETARIAT => 'SEC',
            Role::COMPTABILITE => 'CPT',
            default => 'ADM',
        };
    }
}
