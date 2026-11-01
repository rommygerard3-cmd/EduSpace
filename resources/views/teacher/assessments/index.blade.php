@extends('layouts.app')

@section('title', 'Évaluations')

@section('content')
    <div class="space-y-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Évaluations</h2>
                <p class="text-sm text-slate-500">Gestion des évaluations et publication des notes.</p>
            </div>
            <a href="{{ route('teacher.assessments.create') }}" class="btn-primary">Nouvelle évaluation</a>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Évaluation</th>
                            <th>Classe</th>
                            <th>Matière</th>
                            <th>Notes</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($assessments as $assessment)
                        <tr>
                            <td>{{ $assessment->date?->format('d/m/Y') }}</td>
                            <td class="font-medium text-slate-900">{{ $assessment->title }}</td>
                            <td>{{ $assessment->schoolClass?->name }}</td>
                            <td>{{ $assessment->subject?->name }}</td>
                            <td>{{ $assessment->grades_count }}</td>
                            <td><span class="badge badge-slate">{{ $assessment->status }}</span></td>
                            <td class="text-right">
                                <a href="{{ route('teacher.assessments.edit', $assessment) }}" class="text-sm font-semibold text-blue-700">Modifier</a>
                                <a href="{{ route('teacher.grades.edit', $assessment) }}" class="ml-3 text-sm font-semibold text-slate-600">Notes</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-sm text-slate-500">Aucune évaluation.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
