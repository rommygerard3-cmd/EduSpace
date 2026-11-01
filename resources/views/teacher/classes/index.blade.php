@extends('layouts.app')

@section('title', 'Mes classes')

@section('content')
    <div class="space-y-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Mes classes</h2>
                <p class="text-sm text-slate-500">Affectations de l’année scolaire en cours.</p>
            </div>
            <a href="{{ route('teacher.attendance.index') }}" class="btn-secondary">Présences</a>
        </div>

        <div class="space-y-5">
            @forelse($assignments as $grouped)
                @foreach($grouped as $assignment)
                    <div class="card">
                        <div class="card-body">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <a href="{{ route('teacher.classes.show', $assignment->schoolClass) }}" class="text-lg font-semibold text-blue-700 hover:text-blue-800">{{ $assignment->schoolClass?->displayName() }}</a>
                                    <p class="mt-1 text-sm text-slate-500">{{ $assignment->subject?->name }} · coef. {{ $assignment->coefficient }}</p>
                                </div>
                                <span class="badge badge-blue">{{ $assignment->schoolClass?->activeStudents()->count() }} élève(s)</span>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <a href="{{ route('teacher.classes.show', $assignment->schoolClass) }}" class="btn-secondary btn-sm">Détail de la classe</a>
                                <a href="{{ route('teacher.attendance.index') }}" class="btn-secondary btn-sm">Enregistrer des présences</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @empty
                <div class="card"><div class="card-body text-sm text-slate-500">Aucune classe affectée pour le moment.</div></div>
            @endforelse
        </div>
    </div>
@endsection
