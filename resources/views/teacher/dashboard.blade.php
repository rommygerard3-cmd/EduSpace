@extends('layouts.app')

@section('title', 'Tableau de bord enseignant')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm text-slate-500">Portail enseignant</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">{{ $teacher->fullName() }}</h2>
                <p class="text-sm text-slate-500">{{ $teacher->user?->email ?: 'Compte enseignant' }}</p>
            </div>
            <a href="{{ route('teacher.assessments.create') }}" class="btn-primary">Nouvelle évaluation</a>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="card"><div class="card-body"><p class="stat-label">Classes</p><p class="stat-value mt-2">{{ $assignments->count() }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Élèves suivis</p><p class="stat-value mt-2">{{ number_format($studentsCount) }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Fiches en attente</p><p class="stat-value mt-2">{{ number_format($pendingPreparations) }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Évaluations récentes</p><p class="stat-value mt-2">{{ $recentAssessments->count() }}</p></div></div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4 flex items-center justify-between"><h2>Classes et élèves</h2><a href="{{ route('teacher.classes.index') }}" class="text-sm font-semibold text-blue-700">Voir mes classes</a></div>
                    <div class="space-y-3">
                        @forelse($assignments as $assignment)
                            <div class="flex items-center justify-between gap-3 rounded-lg bg-slate-50 px-3 py-2">
                                <div>
                                    <p class="font-medium">{{ $assignment->schoolClass?->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $assignment->subject?->name }} · coef. {{ $assignment->coefficient }}</p>
                                </div>
                                <span class="badge badge-blue">{{ $assignment->schoolClass?->activeStudents()->count() }} élève(s)</span>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Aucune affectation active.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="mb-4 flex items-center justify-between"><h2>Évaluations récentes</h2><a href="{{ route('teacher.assessments.index') }}" class="text-sm font-semibold text-blue-700">Toutes</a></div>
                    <div class="space-y-3">
                        @forelse($recentAssessments as $assessment)
                            <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3 last:border-0 last:pb-0">
                                <div>
                                    <p class="font-medium text-slate-900">{{ $assessment->title }}</p>
                                    <p class="text-xs text-slate-500">{{ $assessment->schoolClass?->name }} · {{ $assessment->subject?->name }} · {{ $assessment->date?->format('d/m/Y') }}</p>
                                </div>
                                <span class="badge badge-slate">{{ $assessment->status }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Aucune évaluation pour le moment.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="mb-4">Prochaines séances</h2>
                <div class="space-y-2 text-sm">
                    @forelse($schedules as $schedule)
                        <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                            <span>{{ $schedule->day_label }} {{ substr($schedule->start_time, 0, 5) }}–{{ substr($schedule->end_time, 0, 5) }}</span>
                            <span class="font-medium">{{ $schedule->schoolClass?->name }} · {{ $schedule->subject?->name }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Aucune séance planifiée.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
