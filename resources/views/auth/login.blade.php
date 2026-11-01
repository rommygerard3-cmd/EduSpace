@extends('layouts.guest')

@section('title', 'Connexion')

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-900">Connexion</h1>
        <p class="mt-1 text-sm text-slate-500">Utilisez le matricule et le mot de passe fournis par l’administration.</p>
    </div>

    <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
        @csrf
        <div>
            <label for="matricule" class="label">Matricule</label>
            <input id="matricule" name="matricule" value="{{ old('matricule') }}" required autofocus autocomplete="username" class="input" placeholder="Ex. MAT-2026-0001">
        </div>
        <div>
            <label for="password" class="label">Mot de passe</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="input">
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-blue-700 focus:ring-blue-500">
            Se souvenir de moi
        </label>
        <button class="btn-primary w-full">Se connecter</button>
    </form>

    <p class="mt-5 text-center text-sm text-slate-500">
        Compte non activé ?
        <a href="{{ route('activate.show') }}" class="font-semibold text-blue-700 hover:text-blue-800">Activer mon compte</a>
    </p>
@endsection
