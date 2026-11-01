@extends('layouts.app')

@section('title', 'Mon profil enseignant')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="card">
            <div class="card-body">
                <h2 class="text-xl font-bold text-slate-900">{{ $teacher->fullName() }}</h2>
                <p class="text-sm text-slate-500">{{ $teacher->employee_number ?: 'Sans numéro d’employé' }} · {{ $teacher->hire_date?->format('d/m/Y') ?: 'Date d’embauche inconnue' }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3 class="mb-4 text-lg font-semibold text-slate-900">Compte utilisateur</h3>
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div><dt class="stat-label">Nom</dt><dd class="mt-1 font-medium">{{ $teacher->user?->name }}</dd></div>
                    <div><dt class="stat-label">Email</dt><dd class="mt-1">{{ $teacher->user?->email ?: '—' }}</dd></div>
                    <div><dt class="stat-label">Matricule</dt><dd class="mt-1 font-mono font-medium">{{ $teacher->user?->matricule }}</dd></div>
                    <div><dt class="stat-label">Téléphone</dt><dd class="mt-1">{{ $teacher->user?->phone ?: $teacher->phone ?: '—' }}</dd></div>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3 class="mb-4 text-lg font-semibold text-slate-900">Affectations</h3>
                <div class="space-y-2 text-sm">
                    @forelse($teacher->classSubjects as $assignment)
                        <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                            <span>{{ $assignment->schoolClass?->displayName() }}</span>
                            <span class="font-medium">{{ $assignment->subject?->name }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Aucune affectation.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
