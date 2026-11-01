@extends('layouts.app')

@section('title', $schoolClass->displayName())

@section('content')
    <div class="space-y-6">
        <div class="card">
            <div class="card-body flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">{{ $schoolClass->displayName() }}</h2>
                    <p class="text-sm text-slate-500">{{ $schoolClass->academicYear?->name }} · {{ $schoolClass->level?->name }} · {{ $schoolClass->room ?: 'Sans local' }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $schoolClass->enrollments->count() }} élève(s) inscrit(s)</p>
                </div>
                <a href="{{ route('teacher.schedule') }}" class="btn-secondary btn-sm">Mon horaire</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3 class="mb-4 text-lg font-semibold text-slate-900">Élèves de la classe</h3>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Roll</th>
                                <th>Élève</th>
                                <th>Matricule</th>
                                <th>Compte</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($schoolClass->enrollments as $enrollment)
                            <tr>
                                <td>{{ $enrollment->roll_number }}</td>
                                <td class="font-medium text-slate-900">{{ $enrollment->student?->fullName() }}</td>
                                <td class="font-mono text-xs text-slate-600">{{ $enrollment->student?->matricule }}</td>
                                <td>{!! $enrollment->student?->user ? '<span class="badge badge-green">Oui</span>' : '<span class="badge badge-amber">Non</span>' !!}</td>
                                <td class="text-right">
                                    <a href="{{ route('admin.students.show', $enrollment->student) }}" class="text-sm font-semibold text-blue-700">Dossier</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-6 text-center text-sm text-slate-500">Aucun élève inscrit.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
