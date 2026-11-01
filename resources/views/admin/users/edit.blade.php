@extends('layouts.app')

@section('title', 'Modifier un utilisateur')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="card">
            <div class="card-body">
                <h2 class="mb-1">Modifier {{ $user->name }}</h2>
                <p class="mb-5 text-sm text-slate-500">Matricule : <span class="font-mono">{{ $user->matricule }}</span></p>
                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    @method('PUT')
                    @include('admin.users.form', ['isCreate' => false])
                    <div class="flex gap-2 sm:col-span-2">
                        <button class="btn-primary">Enregistrer</button>
                        <a href="{{ route('admin.users.show', $user) }}" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-6">
            <div class="card-body flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2>Activation et mot de passe</h2>
                    <p class="text-sm text-slate-500">Générez un nouveau code d’activation à usage unique.</p>
                </div>
                <div class="flex gap-2">
                    @if(! $user->activated_at)
                        <form method="POST" action="{{ route('admin.users.activate', $user) }}">@csrf<button class="btn-secondary btn-sm">Générer un code d’activation</button></form>
                    @endif
                    <form method="POST" action="{{ route('admin.users.reset-password', $user) }}">@csrf<button class="btn-secondary btn-sm">Réinitialiser le mot de passe</button></form>
                </div>
            </div>
        </div>
    </div>
@endsection
