@extends('layouts.app')

@section('title', 'Mon profil')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="card">
            <div class="card-body">
                <h2 class="mb-4">Identité</h2>
                <dl class="grid gap-4 sm:grid-cols-2 text-sm">
                    <div><dt class="stat-label">Nom</dt><dd class="mt-1 font-medium">{{ $student->fullName() }}</dd></div>
                    <div><dt class="stat-label">Matricule</dt><dd class="mt-1 font-mono font-medium">{{ $student->matricule }}</dd></div>
                    <div><dt class="stat-label">Classe</dt><dd class="mt-1">{{ $student->currentEnrollment?->schoolClass?->displayName() ?: '—' }}</dd></div>
                    <div><dt class="stat-label">Sexe</dt><dd class="mt-1">{{ $student->gender === 'F' ? 'Féminin' : ($student->gender === 'M' ? 'Masculin' : '—') }}</dd></div>
                    <div><dt class="stat-label">Naissance</dt><dd class="mt-1">{{ $student->birth_date?->format('d/m/Y') ?: '—' }}{{ $student->birth_place ? ' à '.$student->birth_place : '' }}</dd></div>
                    <div><dt class="stat-label">Téléphone</dt><dd class="mt-1">{{ $student->phone ?: '—' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="stat-label">Adresse</dt><dd class="mt-1">{{ $student->address ?: '—' }}</dd></div>
                </dl>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h2 class="mb-4">Responsables légaux</h2>
                @forelse($student->guardians as $guardian)
                    <div class="mb-2 rounded-lg bg-slate-50 p-3 text-sm">
                        <p class="font-medium">{{ $guardian->first_name }} {{ $guardian->last_name }}</p>
                        <p class="text-slate-500">{{ $guardian->relationship ?: 'Responsable' }} · {{ $guardian->phone ?: 'Sans téléphone' }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Aucun responsable enregistré.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
