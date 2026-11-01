@extends('layouts.app')

@section('title', 'Mon profil')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        @if($user->must_change_password)
            <div class="alert-warning">Vous devez définir un mot de passe personnel avant de continuer.</div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="card lg:col-span-2">
                <div class="card-body">
                    <h2 class="mb-4">Informations personnelles</h2>
                    @if($user->isStudent())
                        <dl class="grid gap-4 sm:grid-cols-2">
                            <div><dt class="stat-label">Nom</dt><dd class="mt-1 font-medium">{{ $user->name }}</dd></div>
                            <div><dt class="stat-label">Matricule</dt><dd class="mt-1 font-mono font-medium">{{ $user->matricule }}</dd></div>
                            <div><dt class="stat-label">Email</dt><dd class="mt-1">{{ $user->email ?: '—' }}</dd></div>
                            <div><dt class="stat-label">Téléphone</dt><dd class="mt-1">{{ $user->phone ?: '—' }}</dd></div>
                        </dl>
                        <p class="mt-4 text-sm text-slate-500">Les informations scolaires d’un élève sont modifiées par l’administration.</p>
                    @else
                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="grid gap-4 sm:grid-cols-2">
                            @csrf
                            @method('PUT')
                            <div class="sm:col-span-2">
                                <label class="label" for="name">Nom complet</label>
                                <input class="input" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div>
                                <label class="label" for="email">Email</label>
                                <input class="input" type="email" id="email" name="email" value="{{ old('email', $user->email) }}">
                            </div>
                            <div>
                                <label class="label" for="phone">Téléphone</label>
                                <input class="input" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="label" for="photo">Photo (JPG, PNG, 2 Mo max.)</label>
                                <input class="input" type="file" id="photo" name="photo" accept="image/*">
                            </div>
                            <div class="sm:col-span-2">
                                <button class="btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="mb-1">Mot de passe</h2>
                    <p class="mb-4 text-sm text-slate-500">Choisissez un mot de passe robuste et personnel.</p>
                    <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="label" for="current_password">Mot de passe actuel</label>
                            <input class="input" type="password" id="current_password" name="current_password" required autocomplete="current-password">
                        </div>
                        <div>
                            <label class="label" for="password">Nouveau mot de passe</label>
                            <input class="input" type="password" id="password" name="password" required autocomplete="new-password">
                        </div>
                        <div>
                            <label class="label" for="password_confirmation">Confirmation</label>
                            <input class="input" type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                        </div>
                        <button class="btn-primary w-full">Modifier le mot de passe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
