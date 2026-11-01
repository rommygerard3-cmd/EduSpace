@extends('layouts.app')

@section('title', 'Dossier enseignant')

@section('content')
    <div class="space-y-6">
        <div class="card">
            <div class="card-body flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-xl">{{ $teacher->fullName() }}</h2>
                    <p class="text-sm text-slate-500">{{ $teacher->employee_number ?: 'Sans numéro d’employé' }} · {{ $teacher->email ?: 'Sans email' }}</p>
                    <div class="mt-2 flex gap-2">{!! $teacher->user ? '<span class="badge-green">Compte créé</span>' : '<span class="badge-amber">Sans compte</span>' !!}</div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn-secondary btn-sm">Modifier</a>
                    @if(! $teacher->user)
                        <form method="POST" action="{{ route('admin.teachers.account', $teacher) }}">@csrf<button class="btn-primary btn-sm">Créer un compte</button></form>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="card">
                <div class="card-body">
                    <h2 class="mb-4">Affectations</h2>
                    <div class="space-y-2 text-sm">
                        @forelse($teacher->classSubjects as $assignment)
                            <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                                <span>{{ $assignment->schoolClass?->displayName() }}</span>
                                <span class="font-medium">{{ $assignment->subject?->name }} (coef. {{ $assignment->coefficient }})</span>
                            </div>
                        @empty
                            <p class="text-slate-500">Aucune affectation.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="mb-4">Horaire</h2>
                    <div class="space-y-2 text-sm">
                        @forelse($teacher->schedules as $schedule)
                            <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                                <span>{{ \App\Models\Schedule::DAYS[$schedule->day_of_week] ?? $schedule->day_of_week }} {{ substr($schedule->start_time, 0, 5) }}–{{ substr($schedule->end_time, 0, 5) }}</span>
                                <span>{{ $schedule->schoolClass?->name }} · {{ $schedule->subject?->name }}</span>
                            </div>
                        @empty
                            <p class="text-slate-500">Aucun cours programmé.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="mb-4">Évaluations récentes</h2>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead><tr><th>Date</th><th>Classe</th><th>Matière</th><th>Titre</th><th>Statut</th></tr></thead>
                        <tbody>
                        @forelse($teacher->assessments->sortByDesc('date')->take(10) as $assessment)
                            <tr><td>{{ $assessment->date?->format('d/m/Y') }}</td><td>{{ $assessment->schoolClass?->name }}</td><td>{{ $assessment->subject?->name }}</td><td>{{ $assessment->title }}</td><td><span class="badge-slate">{{ $assessment->status }}</span></td></tr>
                        @empty
                            <tr><td colspan="5" class="py-6 text-center text-slate-500">Aucune évaluation.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
