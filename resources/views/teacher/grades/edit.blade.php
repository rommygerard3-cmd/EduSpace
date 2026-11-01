@extends('layouts.app')

@section('title', 'Saisir les notes')

@section('content')
    <div class="space-y-5">
        <div>
            <h2 class="text-xl font-bold text-slate-900">{{ $assessment->title }}</h2>
            <p class="text-sm text-slate-500">{{ $assessment->schoolClass?->displayName() }} · {{ $assessment->subject?->name }} · {{ $assessment->term?->name }} · note sur {{ $assessment->max_score }}</p>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('teacher.grades.update', $assessment) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Élève</th>
                                    <th>Matricule</th>
                                    <th>Note / {{ $assessment->max_score }}</th>
                                    <th>Commentaire</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($students as $enrollment)
                                @php($studentId = $enrollment->student_id)
                                @php($grade = $grades[$studentId] ?? null)
                                <tr>
                                    <td>{{ $enrollment->roll_number }}</td>
                                    <td class="font-medium text-slate-900">{{ $enrollment->student?->fullName() }}</td>
                                    <td class="font-mono text-xs text-slate-600">{{ $enrollment->student?->matricule }}</td>
                                    <td>
                                        <input class="input" type="number" step="0.01" min="0" max="{{ $assessment->max_score }}" name="scores[{{ $studentId }}]" value="{{ $grade?->score }}" placeholder="—">
                                    </td>
                                    <td>
                                        <textarea class="input" rows="2" name="comments[{{ $studentId }}]" placeholder="Commentaire optionnel">{{ $grade?->comment }}</textarea>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn-primary" type="submit">Enregistrer les notes</button>
                        <a href="{{ route('teacher.assessments.index') }}" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
