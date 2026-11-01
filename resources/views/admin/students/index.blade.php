@extends('layouts.app')

@section('title', 'Élèves')

@section('content')
    <div class="space-y-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" class="flex flex-1 flex-col gap-2 md:flex-row">
                <input class="input md:max-w-xs" type="search" name="q" value="{{ request('q') }}" placeholder="Nom ou matricule">
                <select class="input md:max-w-48" name="class">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected(request('class') == $class->id)>{{ $class->displayName() }}</option>
                    @endforeach
                </select>
                <select class="input md:max-w-40" name="status">
                    <option value="">Tous les états</option>
                    <option value="active" @selected(request('status') === 'active')>Actifs</option>
                    <option value="archived" @selected(request('status') === 'archived')>Archivés</option>
                </select>
                <button class="btn-secondary">Filtrer</button>
            </form>
            <a href="{{ route('admin.students.create') }}" class="btn-primary">Inscrire un élève</a>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Élève</th><th>Matricule</th><th>Classe</th><th>Compte</th><th>État</th><th></th></tr></thead>
                    <tbody>
                    @forelse($students as $student)
                        @php($enrollment = $student->enrollments->firstWhere('academic_year_id', $year?->id) ?? $student->enrollments->first())
                        <tr>
                            <td>
                                <p class="font-medium text-slate-900">{{ $student->fullName() }}</p>
                                <p class="text-xs text-slate-500">{{ $student->gender === 'F' ? 'Fille' : ($student->gender === 'M' ? 'Garçon' : '—') }} · {{ $student->phone ?: 'Sans téléphone' }}</p>
                            </td>
                            <td class="font-mono text-xs">{{ $student->matricule }}</td>
                            <td>{{ $enrollment?->schoolClass?->displayName() ?? 'Non inscrit' }}</td>
                            <td>{!! $student->user ? '<span class="badge-green">Oui</span>' : '<span class="badge-amber">Non</span>' !!}</td>
                            <td>{!! $student->status === 'active' ? '<span class="badge-green">Actif</span>' : '<span class="badge-slate">Archivé</span>' !!}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.students.show', $student) }}" class="text-sm font-semibold text-blue-700">Voir</a>
                                <a href="{{ route('admin.students.edit', $student) }}" class="ml-3 text-sm font-semibold text-slate-600">Modifier</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-slate-500">Aucun élève trouvé.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{ $students->links() }}
    </div>
@endsection
