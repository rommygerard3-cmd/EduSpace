@extends('layouts.app')

@section('title', 'Discipline - Incidents')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold">Incidents</h2>
                <a href="{{ route('discipline.incidents.create') }}" class="btn-primary">Nouvel incident</a>
            </div>

            <form method="GET" action="{{ route('discipline.incidents.index') }}" class="mb-6 flex gap-2">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Eleve, matricule..." class="input w-56" aria-label="Rechercher">
                <select name="status" class="input w-56">
                    <option value="">Tous les statuts</option>
                    <option value="open" @selected(request('status') === 'open')>open</option>
                    <option value="resolved" @selected(request('status') === 'resolved')>resolved</option>
                    <option value="closed" @selected(request('status') === 'closed')>closed</option>
                </select>
                <button type="submit" class="btn-secondary">Filtrer</button>
            </form>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr><th>Eleve</th><th>Matricule</th><th>Type</th><th>Date</th><th>Statut</th><th></th></tr>
                    </thead>
                    <tbody>
                    @forelse($incidents as $incident)
                        <tr>
                            <td>{{ $incident->student?->fullName() }}</td>
                            <td class="font-mono text-xs">{{ $incident->student?->matricule }}</td>
                            <td>{{ $incident->type }}</td>
                            <td class="text-xs">{{ $incident->occurred_at?->format('d/m/Y') }}</td>
                            <td><span class="badge badge-red">{{ $incident->status }}</span></td>
                            <td class="text-right"><a href="{{ route('discipline.incidents.edit', $incident) }}" class="text-sm font-semibold text-blue-700">Gerer</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-slate-500">Aucun incident.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
