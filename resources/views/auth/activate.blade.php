@extends('layouts.guest')

@section('title', 'Activer mon compte')

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-900">Activer mon compte</h1>
        <p class="mt-1 text-sm text-slate-500">Saisissez le matricule, le code d’activation remis par l’administration et choisissez votre mot de passe.</p>
    </div>

    <form method="POST" action="{{ route('activate.store') }}" class="space-y-4">
        @csrf
        <div>
            <label for="matricule" class="label">Matricule</label>
            <input id="matricule" name="matricule" value="{{ old('matricule', $matricule) }}" required class="input">
        </div>
        <div>
            <label for="activation_code" class="label">Code d’activation</label>
            <input id="activation_code" name="activation_code" value="{{ old('activation_code') }}" required class="input font-mono" autocomplete="one-time-code">
        </div>
        <div>
            <label for="password" class="label">Nouveau mot de passe</label>
            <input id="password" type="password" name="password" required class="input" autocomplete="new-password">
            <p class="mt-1 text-xs text-slate-500">8 caractères minimum, avec lettres et chiffres.</p>
        </div>
        <div>
            <label for="password_confirmation" class="label">Confirmer le mot de passe</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="input" autocomplete="new-password">
        </div>
        <button class="btn-primary w-full">Activer et me connecter</button>
    </form>

    <p class="mt-5 text-center text-sm text-slate-500">
        <a href="{{ route('login') }}" class="font-semibold text-blue-700 hover:text-blue-800">Retour à la connexion</a>
    </p>
@endsection
