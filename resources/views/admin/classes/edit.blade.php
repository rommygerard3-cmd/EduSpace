@extends('layouts.app')

@section('title', 'Modifier une classe')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="card">
            <div class="card-body">
                <h2 class="mb-1">Modifier {{ $schoolClass->name }}</h2>
                <p class="mb-5 text-sm text-slate-500">{{ $schoolClass->academicYear?->name }}</p>
                <form method="POST" action="{{ route('admin.classes.update', $schoolClass) }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    @method('PUT')
                    @include('admin.classes.form')
                    <div class="flex gap-2 sm:col-span-2">
                        <button class="btn-primary">Enregistrer</button>
                        <a href="{{ route('admin.classes.show', $schoolClass) }}" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
