@extends('layouts.app')

@section('title', 'Rapports pédagogiques')

@section('content')
    <div class="space-y-5">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.reports.csv', ['type' => 'results']) }}" class="btn-secondary btn-sm">Exporter les résultats (CSV)</a>
            <a href="{{ route('admin.reports.csv', ['type' => 'attendance']) }}" class="btn-secondary btn-sm">Exporter les présences (CSV)</a>
        </div>
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Date</th><th>Enseignant</th><th>Classe</th><th>Matière</th><th>Sujet</th><th>Statut</th></tr></thead>
                    <tbody>
                    @forelse($preparations as $preparation)
                        <tr>
                            <td>{{ $preparation->date?->format('d/m/Y') }}</td>
                            <td>{{ $preparation->teacher?->fullName() }}</td>
                            <td>{{ $preparation->schoolClass?->name }}</td>
                            <td>{{ $preparation->subject?->name }}</td>
                            <td>{{ $preparation->topic }}</td>
                            <td><span class="badge-slate">{{ $preparation->statusLabel() }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-slate-500">Aucune fiche de préparation.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
