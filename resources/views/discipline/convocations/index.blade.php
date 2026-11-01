@extends('layouts.app')

@section('title', 'Discipline - Convocations')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold">Convocations</h2>
                <a href="{{ route('discipline.convocations.create') }}" class="btn-primary">Nouvelle convocation</a>
            </div>

            <form method="GET" action="{{ route('discipline.convocations.index') }}" class="mb-6 flex gap-2">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Eleve, matricule..." class="input w-56" aria-label="Rechercher">
                <button type="submit" class="btn-secondary">Filtrer</button>
            </form>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr><th>Eleve</th><th>Matricule</th><th>Rendez-vous</th><th>Motif</th><th>Statut</th><th></th></tr>
                    </thead>
                    <tbody>
                    @forelse($convocations as $convocation)
                        <tr>
                            <td>{{ $convocation->student?->fullName() }}</td>
                            <td class="font-mono text-xs">{{ $convocation->student?->matricule }}</td>
                            <td class="text-xs">{{ $convocation->meeting_at?->format('d/m/Y H:i') }}</td>
                            <td class="text-xs">{{ $convocation->reason }}</td>
                            <td><span class="badge badge-amber">{{ $convocation->status }}</span></td>
                            <td class="text-right"><a href="{{ route('discipline.convocations.edit', $convocation) }}" class="text-sm font-semibold text-blue-700">Gerer</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-slate-500">Aucune convocation.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
