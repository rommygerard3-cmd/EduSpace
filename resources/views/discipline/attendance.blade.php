@extends('layouts.app')

@section('title', 'Discipline - Presences')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="mb-4 flex flex-wrap items-center gap-4">
                <h2 class="text-lg font-semibold">Absences et retards</h2>
                <form method="GET" action="{{ route('discipline.attendance') }}" class="flex gap-2">
                    <select name="class" class="input w-64">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" @selected(optional($selectedClass)->id == $class->id)>{{ $class->displayName() }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="date" value="{{ $date }}" class="input w-56">
                    <button type="submit" class="btn-secondary">Filtrer</button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr><th>Eleve</th><th>Matricule</th><th>Classe</th><th>Matiere</th><th>Date</th><th>Statut</th><th>Observation</th></tr>
                    </thead>
                    <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>{{ $record->student?->fullName() }}</td>
                            <td class="font-mono text-xs">{{ $record->student?->matricule }}</td>
                            <td>{{ optional($record->schoolClass)->displayName() }}</td>
                            <td>{{ optional($record->subject)->name }}</td>
                            <td class="text-xs">{{ $record->date?->format('d/m/Y') }}</td>
                            <td><span class="badge badge-{{ $record->status === 'absent' ? 'red' : 'amber' }}">{{ $record->status }}</span></td>
                            <td class="text-xs">{{ $record->observation }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-6 text-center text-slate-500">Aucun enregistrement.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
