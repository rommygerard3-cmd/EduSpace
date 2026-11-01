@extends('layouts.app')

@section('title', 'Tableau de bord élève')

@section('content')
    <div class="space-y-6">
        <div>
            <p class="text-sm text-slate-500">Portail élève</p>
            <h2 class="mt-1 text-2xl font-bold text-slate-900">{{ $student->fullName() }}</h2>
            <p class="text-sm text-slate-500">{{ $student->matricule }} · {{ $class?->displayName() ?? 'Non inscrit' }} · {{ $year?->name ?? 'Aucune année active' }}</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="card"><div class="card-body"><p class="stat-label">Moyenne</p><p class="stat-value mt-2">{{ $results['average'] !== null ? number_format($results['average'], 2) : '—' }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Mention</p><p class="stat-value mt-2 text-lg">{{ $results['mention'] ?? '—' }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Absences / retards</p><p class="stat-value mt-2">{{ $recentAbsences->count() }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Solde scolaire</p><p class="stat-value mt-2">{{ number_format($invoices->sum(fn ($invoice) => $invoice->balance()), 2, ',', ' ') }}</p></div></div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4 flex items-center justify-between"><h2>Prochains cours</h2><a href="{{ route('student.schedule') }}" class="text-sm font-semibold text-blue-700">Emploi du temps</a></div>
                    <div class="space-y-2 text-sm">
                        @forelse($nextSchedules as $schedule)
                            <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                                <span>{{ $schedule->dayLabel() }} {{ substr((string) $schedule->start_time, 0, 5) }}–{{ substr((string) $schedule->end_time, 0, 5) }}</span>
                                <span class="font-medium">{{ $schedule->subject?->name }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Aucun cours planifié pour votre classe.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="mb-4 flex items-center justify-between"><h2>Absences récentes</h2></div>
                    <div class="space-y-2 text-sm">
                        @forelse($recentAbsences as $record)
                            <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                                <span>{{ $record->date?->format('d/m/Y') }} · {{ $record->statusLabel() }}</span>
                                <span class="text-slate-500">{{ $record->subject?->name ?? $record->session }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Aucune absence ni retard enregistré.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
