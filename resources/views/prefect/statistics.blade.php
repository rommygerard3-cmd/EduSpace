@extends('layouts.app')

@section('title', 'Statistiques pédagogiques')

@section('content')
    <div class="space-y-6">
        <p class="text-sm text-slate-500">{{ $year?->name }} · {{ $term?->name ?? 'Aucune période' }}</p>
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Classe</th><th>Élèves</th><th>Moyenne</th><th>Réussite</th><th>Absences</th><th>Retards</th><th>Présence</th></tr></thead>
                    <tbody>
                    @forelse($classStats as $row)
                        <tr>
                            <td class="font-medium">{{ $row['class']->displayName() }}</td>
                            <td>{{ $row['students'] }}</td>
                            <td>{{ $row['average'] !== null ? number_format($row['average'], 2) : '—' }}</td>
                            <td>{{ $row['success_rate'] !== null ? $row['success_rate'].' %' : '—' }}</td>
                            <td>{{ $row['attendance']['absences'] }}</td>
                            <td>{{ $row['attendance']['late'] }}</td>
                            <td>{{ $row['attendance']['rate'] !== null ? $row['attendance']['rate'].' %' : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-slate-500">Aucune statistique calculable.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h2 class="mb-3">Absences par classe</h2>
                @forelse($absencesByClass as $row)
                    <div class="flex justify-between rounded-lg bg-slate-50 px-3 py-2 text-sm">
                        <span>{{ $row->schoolClass?->displayName() }}</span>
                        <span class="font-semibold">{{ $row->total }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Aucune absence enregistrée.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
