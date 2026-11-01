@extends('layouts.app')

@section('title', 'Présences')

@section('content')
    <div class="space-y-5">
        <div class="card">
            <div class="card-body">
                <h2 class="mb-4 text-lg font-bold text-slate-900">Filtres</h2>
                <form method="GET" action="{{ route('teacher.attendance.index') }}" class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label" for="class_id">Classe</label>
                        <select class="input" id="class_id" name="class_id">
                            <option value="">Sélectionner</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" @selected($selectedClass?->id == $class->id)>{{ $class->displayName() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label" for="date">Date</label>
                        <input class="input" type="date" id="date" name="date" value="{{ $date }}">
                    </div>
                    <div>
                        <label class="label" for="session">Session</label>
                        <select class="input" id="session" name="session">
                            <option value="morning" @selected($session === 'morning')>Matin</option>
                            <option value="afternoon" @selected($session === 'afternoon')>Après-midi</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button class="btn-primary" type="submit">Filtrer</button>
                    </div>
                </form>
            </div>
        </div>

        @if($selectedClass)
            <div class="card">
                <div class="card-body">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ $selectedClass->displayName() }}</h3>
                            <p class="text-sm text-slate-500">{{ $date }} · {{ $session === 'morning' ? 'Matin' : 'Après-midi' }}</p>
                        </div>
                        <form method="POST" action="{{ route('teacher.attendance.store') }}">
                            @csrf
                            <input type="hidden" name="school_class_id" value="{{ $selectedClass->id }}">
                            <input type="hidden" name="date" value="{{ $date }}">
                            <input type="hidden" name="session" value="{{ $session }}">
                            <button class="btn-primary btn-sm" type="submit">Enregistrer</button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Roll</th>
                                    <th>Élève</th>
                                    <th>Statut</th>
                                    <th>Observation</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($students as $enrollment)
                                @php($studentId = $enrollment->student_id)
                                @php($record = $records[$studentId] ?? null)
                                <tr>
                                    <td>{{ $enrollment->roll_number }}</td>
                                    <td class="font-medium text-slate-900">{{ $enrollment->student?->fullName() }}</td>
                                    <td>
                                        <select class="input w-40" name="statuses[{{ $studentId }}]">
                                            @foreach($statuses as $value => $label)
                                                <option value="{{ $value }}" @selected(($record?->status ?? 'present') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input class="input" name="observations[{{ $studentId }}]" value="{{ $record?->observation }}" placeholder="Optionnel">
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-6 text-center text-sm text-slate-500">Aucun élève inscrit.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="card"><div class="card-body text-sm text-slate-500">Sélectionnez une classe pour enregistrer des présences.</div></div>
        @endif
    </div>
@endsection
