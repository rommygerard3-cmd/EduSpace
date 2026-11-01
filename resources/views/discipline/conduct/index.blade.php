@extends('layouts.app')

@section('title', 'Discipline - Notes de conduite')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="mb-4 flex flex-wrap items-center gap-4">
                <h2 class="text-lg font-semibold">Notes de conduite</h2>
                <form method="GET" action="{{ route('discipline.conduct.index') }}" class="flex gap-2">
                    <select name="term" class="input w-56">
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" @selected(optional($selectedTerm)->id == $term->id)>{{ optional($term->academicYear)->name }} - {{ $term->name }}</option>
                        @endforeach
                    </select>
                    <select name="class" class="input w-64">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" @selected(optional($selectedClass)->id == $class->id)>{{ $class->displayName() }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-secondary">Filtrer</button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr><th>Eleve</th><th>Matricule</th><th>Trimestre</th><th>Score /20</th><th>Commentaire</th></tr>
                    </thead>
                    <tbody>
                    @forelse($notes as $note)
                        <tr>
                            <td>{{ $note->student?->fullName() }}</td>
                            <td class="font-mono text-xs">{{ $note->student?->matricule }}</td>
                            <td>{{ optional($note->term)->name }}</td>
                            <td class="font-semibold">{{ $note->score }}</td>
                            <td class="text-xs">{{ $note->comment }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-slate-500">Aucune note de conduite.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
