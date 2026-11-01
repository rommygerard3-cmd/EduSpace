@extends('layouts.app')

@section('title', 'Modifier un enseignant')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="card">
            <div class="card-body">
                <h2 class="mb-5">Modifier {{ $teacher->fullName() }}</h2>
                <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    @method('PUT')
                    @include('admin.teachers.form', ['isCreate' => false])
                    <div class="flex gap-2 sm:col-span-2">
                        <button class="btn-primary">Enregistrer</button>
                        <a href="{{ route('admin.teachers.show', $teacher) }}" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
