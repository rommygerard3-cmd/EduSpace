@extends('layouts.app')

@section('title', 'Classe '.$schoolClass->name)

@section('content')
    <div class="space-y-6">
        <div class="card">
            <div class="card-body flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-xl">{{ $schoolClass->displayName() }}</h2>
                    <p class="text-sm text-slate-500">{{ $schoolClass->academicYear?->name }} · {{ $schoolClass->level?->name }} · {{ $schoolClass->room ?: 'Sans local' }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $schoolClass->enrollments->count() }} élève(s) sur {{ $schoolClass->capacity }} places</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.schedules.create') }}" class="btn-secondary btn-sm">Ajouter un cours</a>
                    <a href="{{ route('admin.classes.edit', $schoolClass) }}" class="btn-secondary btn-sm">Modifier</a>
                    <form method="POST" action="{{ route('admin.classes.destroy', $schoolClass) }}" data-confirm="Supprimer cette classe ?">@csrf @method('DELETE')<button class="btn-danger btn-sm">Supprimer</button></form>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="card">
                <div class="card-body">
                    <h2 class="mb-4">Matières et enseignants</h2>
                    <div class="space-y-2 text-sm">
                        @forelse($schoolClass->classSubjects as $assignment)
                            <div class="flex items-center justify-between gap-3 rounded-lg bg-slate-50 px-3 py-2">
                                <div><p class="font-medium">{{ $assignment->subject?->name }}</p><p class="text-xs text-slate-500">{{ $assignment->teacher?->fullName() }} · coef. {{ $assignment->coefficient }}</p></div>
                                <form method="POST" action="{{ route('admin.class-subjects.destroy', $assignment) }}" data-confirm="Retirer cette affectation ?">@csrf @method('DELETE')<button class="text-xs font-semibold text-red-600">Retirer</button></form>
                            </div>
                        @empty
                            <p class="text-slate-500">Aucune matière affectée.</p>
                        @endforelse
                    </div>
                    <form method="POST" action="{{ route('admin.classes.subjects.store', $schoolClass) }}" class="mt-4 grid gap-3 rounded-lg border border-slate-200 p-3 sm:grid-cols-2">
                        @csrf
                        <div class="sm:col-span-2"><label class="label">Matière</label><select class="input" name="subject_id" required><option value="">Sélectionner</option>@foreach($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->name }}</option>@endforeach</select></div>
                        <div class="sm:col-span-2"><label class="label">Enseignant</label><select class="input" name="teacher_id" required><option value="">Sélectionner</option>@foreach($teachers as $teacher)<option value="{{ $teacher->id }}">{{ $teacher->fullName() }}</option>@endforeach</select></div>
                        <div><label class="label">Coefficient</label><input class="input" type="number" step="0.5" min="0.5" max="20" name="coefficient" value="1" required></div>
                        <div class="flex items-end"><button class="btn-primary w-full">Affecter</button></div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="mb-4">Emploi du temps</h2>
                    <div class="space-y-2 text-sm">
                        @forelse($schoolClass->schedules->sortBy([['day_of_week', 'asc'], ['start_time', 'asc']]) as $schedule)
                            <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                                <span>{{ \App\Models\Schedule::DAYS[$schedule->day_of_week] ?? $schedule->day_of_week }} {{ substr($schedule->start_time, 0, 5) }}–{{ substr($schedule->end_time, 0, 5) }}</span>
                                <span>{{ $schedule->subject?->name }} · {{ $schedule->teacher?->fullName() }}</span>
                            </div>
                        @empty
                            <p class="text-slate-500">Aucun cours programmé.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="mb-4">Élèves inscrits</h2>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead><tr><th>#</th><th>Élève</th><th>Matricule</th><th>Compte</th><th></th></tr></thead>
                        <tbody>
                        @forelse($schoolClass->enrollments as $enrollment)
                            <tr>
                                <td>{{ $enrollment->roll_number }}</td>
                                <td class="font-medium">{{ $enrollment->student?->fullName() }}</td>
                                <td class="font-mono text-xs">{{ $enrollment->student?->matricule }}</td>
                                <td>{!! $enrollment->student?->user ? '<span class="badge-green">Oui</span>' : '<span class="badge-amber">Non</span>' !!}</td>
                                <td class="text-right"><a href="{{ route('admin.students.show', $enrollment->student) }}" class="text-sm font-semibold text-blue-700">Dossier</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-6 text-center text-slate-500">Aucun élève inscrit.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
