@extends('layouts.app')

@section('title', 'Nouvel utilisateur')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="card">
            <div class="card-body">
                <h2 class="mb-1">Créer un compte</h2>
                <p class="mb-5 text-sm text-slate-500">Un code d’activation à usage unique sera généré et affiché une seule fois.</p>
                <form method="POST" action="{{ route('admin.users.store') }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    @include('admin.users.form', ['user' => $user, 'isCreate' => true])
                    <div class="flex gap-2 sm:col-span-2">
                        <button class="btn-primary">Créer le compte</button>
                        <a href="{{ route('admin.users.index') }}" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
