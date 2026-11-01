@extends('layouts.app')

@section('title', 'Discipline - Eleves')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="mb-4 flex flex-wrap items-center gap-4">
                <h2 class="text-lg font-semibold">Eleves sous supervision</h2>
                <form method="GET" action="{{ route('discipline.students') }}" class="flex gap-2">
                    <select name="class" class="input w-64">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" @selected(optional($selectedClass)->id == $class->id)>{{ $class->displayName() }}</option>
                        @endforeach
                    </select>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Nom, matricule..." class="input w-56" aria-label="Rechercher">
                    <button type="submit" class="btn-secondary">Filtrer</button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr><th>Eleve</th><th>Matricule</th><th>Classe</th><th>Incidents</th><th>Convocations</th></tr>
                    </thead>
                    <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>{{ $student->fullName() }}</td>
                            <td class="font-mono text-xs">{{ $student->matricule }}</td>
                            <td>{{ optional($student->currentEnrollment?->schoolClass)->displayName() }}</td>
                            <td>{{ $student->incidents_count }}</td>
                            <td>{{ $student->convocations_count }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-slate-500">Aucun eleve.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
