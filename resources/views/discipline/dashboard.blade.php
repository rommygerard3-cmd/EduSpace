@extends('layouts.app')

@section('title', 'Discipline')

@section('content')
    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="card"><div class="card-body"><p class="stat-label">Eleves suivis</p><p class="stat-value mt-2">{{ $stats['students'] }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Incidents ouverts</p><p class="stat-value mt-2 text-red-700">{{ $stats['open_incidents'] }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Convocations en attente</p><p class="stat-value mt-2">{{ $stats['convocations'] }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Absences</p><p class="stat-value mt-2">{{ $stats['absences'] }}</p></div></div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="card">
                <div class="card-body">
                    <h2 class="mb-4 text-lg font-semibold">Incidents recents</h2>
                    <div class="space-y-2 text-sm">
                        @forelse($recentIncidents as $incident)
                            <a href="{{ route('discipline.incidents.edit', $incident) }}" class="block rounded-lg bg-slate-50 px-3 py-2 hover:bg-slate-100">
                                <span class="font-medium">{{ $incident->student?->fullName() }}</span>
                                <span class="ml-2 text-xs text-slate-500">{{ $incident->occurred_at?->format('d/m/Y') }}</span>
                                <span class="ml-2 text-xs font-semibold uppercase text-red-600">{{ $incident->status }}</span>
                            </a>
                        @empty
                            <p class="text-slate-500">Aucun incident.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="mb-4 text-lg font-semibold">Convocations recentes</h2>
                    <div class="space-y-2 text-sm">
                        @forelse($recentConvocations as $convocation)
                            <a href="{{ route('discipline.convocations.edit', $convocation) }}" class="block rounded-lg bg-slate-50 px-3 py-2 hover:bg-slate-100">
                                <span class="font-medium">{{ $convocation->student?->fullName() }}</span>
                                <span class="ml-2 text-xs text-slate-500">{{ $convocation->meeting_at?->format('d/m/Y') }}</span>
                                <span class="ml-2 text-xs font-semibold uppercase text-amber-600">{{ $convocation->status }}</span>
                            </a>
                        @empty
                            <p class="text-slate-500">Aucune convocation.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
