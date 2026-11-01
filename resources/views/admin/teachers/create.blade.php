@extends('layouts.app')

@section('title', 'Nouvel enseignant')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="card">
            <div class="card-body">
                <h2 class="mb-5">Créer un enseignant</h2>
                <form method="POST" action="{{ route('admin.teachers.store') }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    @include('admin.teachers.form', ['isCreate' => true])
                    <div class="flex gap-2 sm:col-span-2">
                        <button class="btn-primary">Créer l’enseignant</button>
                        <a href="{{ route('admin.teachers.index') }}" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
