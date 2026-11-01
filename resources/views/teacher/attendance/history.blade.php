@extends('layouts.app')

@section('title', 'Enseignement - Historique des presences')

@section('content')
    <div class="card">
        <div class="card-body">
            <h2 class="mb-6 text-lg font-semibold">Historique des presences</h2>

            @forelse($records as $key => $group)
                @php($first = $group->first())
                <div class="mb-6">
                    <h3 class="mb-3 text-base font-semibold">{{ optional($first)->date?->format('d/m/Y') }} - {{ optional($first)->session }} - {{ optional($first->schoolClass)->displayName() }}</h3>
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr><th>Eleve</th><th>Matricule</th><th>Matiere</th><th>Statut</th><th>Observation</th></tr>
                            </thead>
                            <tbody>
                            @foreach($group as $record)
                                <tr>
                                    <td>{{ $record->student?->fullName() }}</td>
                                    <td class="font-mono text-xs">{{ $record->student?->matricule }}</td>
                                    <td>{{ optional($record->subject)->name }}</td>
                                    <td><span class="badge badge-{{ $record->status === 'absent' ? 'red' : 'amber' }}">{{ $record->status }}</span></td>
                                    <td class="text-xs">{{ $record->observation }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <p class="text-slate-500">Aucun historique de presence.</p>
            @endforelse
        </div>
    </div>
@endsection
