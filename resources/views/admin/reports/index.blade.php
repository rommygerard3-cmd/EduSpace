@extends('layouts.app')

@section('title', 'Rapports')

@section('content')
    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="card"><div class="card-body"><p class="stat-label">Eleves actifs</p><p class="stat-value mt-2">{{ $totals['students'] }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Classes</p><p class="stat-value mt-2">{{ $totals['classes'] }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Facture</p><p class="stat-value mt-2">{{ number_format($finance['invoiced'], 2, ',', ' ') }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Encaisse</p><p class="stat-value mt-2 text-emerald-700">{{ number_format($finance['paid'], 2, ',', ' ') }}</p></div></div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="mb-4 text-lg font-semibold">Statistiques par classe</h2>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr><th>Classe</th><th>Eleves</th><th>Moyenne</th><th>Taux de reussite</th><th>Presences</th></tr>
                        </thead>
                        <tbody>
                        @forelse($classStats as $stat)
                            <tr>
                                <td>{{ $stat['class']->displayName() }}</td>
                                <td>{{ $stat['students'] }}</td>
                                <td>{{ $stat['average'] !== null ? number_format((float) $stat['average'], 2, ',', ' ') : 'N/A' }}</td>
                                <td>{{ $stat['success_rate'] !== null ? number_format((float) $stat['success_rate'], 2, ',', ' ') . '%' : 'N/A' }}</td>
                                <td>{{ number_format($stat['attendance']['rate'] ?? 0, 2, ',', ' ') . '%' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-6 text-center text-slate-500">Aucune donnee.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
