@extends('layouts.app')

@section('title', 'Inscrire un élève')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="card">
            <div class="card-body">
                <h2 class="mb-1">Nouvelle inscription</h2>
                <p class="mb-5 text-sm text-slate-500">L’élève sera inscrit dans la classe et l’année scolaire active.</p>
                <form method="POST" action="{{ route('admin.students.store') }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    @include('admin.students.form', ['isCreate' => true, 'currentEnrollment' => null])
                    <div class="flex gap-2 sm:col-span-2">
                        <button class="btn-primary">Inscrire l’élève</button>
                        <a href="{{ route('admin.students.index') }}" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
