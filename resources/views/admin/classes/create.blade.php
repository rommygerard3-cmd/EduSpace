@extends('layouts.app')

@section('title', 'Nouvelle classe')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="card">
            <div class="card-body">
                <h2 class="mb-1">Créer une classe</h2>
                <p class="mb-5 text-sm text-slate-500">Année active : {{ $year->name }}</p>
                <form method="POST" action="{{ route('admin.classes.store') }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    @include('admin.classes.form', ['schoolClass' => null])
                    <div class="flex gap-2 sm:col-span-2">
                        <button class="btn-primary">Créer la classe</button>
                        <a href="{{ route('admin.classes.index') }}" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
