@extends('layouts.app')

@section('title', 'Enseignants')

@section('content')
    <div class="space-y-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" class="flex flex-1 gap-2">
                <input class="input sm:max-w-sm" type="search" name="q" value="{{ request('q') }}" placeholder="Nom ou matricule">
                <button class="btn-secondary">Rechercher</button>
            </form>
            <a href="{{ route('admin.teachers.create') }}" class="btn-primary">Nouvel enseignant</a>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Enseignant</th><th>Matricule</th><th>Classes / matières</th><th>Compte</th><th></th></tr></thead>
                    <tbody>
                    @forelse($teachers as $teacher)
                        <tr>
                            <td><p class="font-medium text-slate-900">{{ $teacher->fullName() }}</p><p class="text-xs text-slate-500">{{ $teacher->phone ?: 'Sans téléphone' }}</p></td>
                            <td class="font-mono text-xs">{{ $teacher->user?->matricule ?? $teacher->employee_number ?? '—' }}</td>
                            <td class="text-xs text-slate-600">
                                {{ $teacher->classSubjects->map(fn ($item) => ($item->schoolClass?->name ?? '').' · '.($item->subject?->name ?? ''))->filter()->join(', ') ?: 'Aucune affectation' }}
                            </td>
                            <td>{!! $teacher->user ? '<span class="badge-green">Oui</span>' : '<span class="badge-amber">Non</span>' !!}</td>
                            <td class="text-right"><a href="{{ route('admin.teachers.show', $teacher) }}" class="text-sm font-semibold text-blue-700">Voir</a><a href="{{ route('admin.teachers.edit', $teacher) }}" class="ml-3 text-sm font-semibold text-slate-600">Modifier</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-slate-500">Aucun enseignant.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{ $teachers->links() }}
    </div>
@endsection
