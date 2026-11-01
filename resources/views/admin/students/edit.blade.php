@extends('layouts.app')

@section('title', 'Modifier un élève')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="card">
            <div class="card-body">
                <h2 class="mb-1">Modifier {{ $student->fullName() }}</h2>
                <p class="mb-5 text-sm text-slate-500">Matricule : <span class="font-mono">{{ $student->matricule }}</span></p>
                <form method="POST" action="{{ route('admin.students.update', $student) }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    @method('PUT')
                    @include('admin.students.form', ['isCreate' => false])
                    <div class="flex gap-2 sm:col-span-2">
                        <button class="btn-primary">Enregistrer</button>
                        <a href="{{ route('admin.students.show', $student) }}" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
