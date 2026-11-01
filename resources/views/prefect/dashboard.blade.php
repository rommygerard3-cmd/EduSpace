@extends('layouts.app')

@section('title', 'Préfecture')

@section('content')
    <div class="space-y-6">
        <div>
            <p class="text-sm text-slate-500">Supervision pédagogique</p>
            <h2 class="mt-1 text-2xl font-bold">{{ $year?->name ?? 'Aucune année active' }}{{ $term ? ' · '.$term->name : '' }}</h2>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="card"><div class="card-body"><p class="stat-label">Enseignants</p><p class="stat-value mt-2">{{ $stats['teachers'] }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Classes</p><p class="stat-value mt-2">{{ $stats['classes'] }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Cours planifiés</p><p class="stat-value mt-2">{{ $stats['schedules'] }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Fiches à examiner</p><p class="stat-value mt-2">{{ $stats['pending_preparations'] }}</p></div></div>
        </div>
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Classe</th><th>Effectif</th><th>Moyenne</th><th>Réussite</th><th>Présence</th></tr></thead>
                    <tbody>
                    @forelse($classStats as $row)
                        <tr>
                            <td class="font-medium">{{ $row['class']->displayName() }}</td>
                            <td>{{ $row['class']->enrollments_count }}</td>
                            <td>{{ $row['average'] !== null ? number_format($row['average'], 2) : '—' }}</td>
                            <td>{{ $row['success_rate'] !== null ? $row['success_rate'].' %' : '—' }}</td>
                            <td>{{ $row['attendance']['rate'] !== null ? $row['attendance']['rate'].' %' : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-slate-500">Aucune donnée pédagogique pour la période en cours.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
