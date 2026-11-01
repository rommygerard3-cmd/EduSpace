@extends('layouts.app')

@section('title', 'Discipline - Gerer l incident')

@section('content')
    <div class="card">
        <div class="card-body">
            <h2 class="mb-6 text-lg font-semibold">Gerer l incident</h2>
            <form method="POST" action="{{ route('discipline.incidents.update', $incident) }}" class="max-w-2xl space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Eleve</label>
                    <select name="student_id" class="input" required>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" @selected($student->id == $incident->student_id)>{{ $student->fullName() }} ({{ $student->matricule }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Date de l'incident</label>
                    <input type="datetime-local" name="occurred_at" class="input" value="{{ $incident->occurred_at ? $incident->occurred_at->toDateTimeLocalString() : '' }}" required>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Type</label>
                    <input type="text" name="type" class="input" value="{{ $incident->type }}" required maxlength="100">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Description</label>
                    <textarea name="description" class="input" rows="4" required>{{ $incident->description }}</textarea>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Sanction proposee</label>
                    <textarea name="sanction" class="input" rows="3">{{ $incident->sanction }}</textarea>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Statut</label>
                    <select name="status" class="input" required>
                        @foreach(['open', 'resolved', 'closed'] as $statusOption)
                            <option value="{{ $statusOption }}" @selected($incident->status === $statusOption)>{{ $statusOption }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('discipline.incidents.index') }}" class="btn-secondary">Annuler</a>
                    <button type="submit" class="btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
@endsection
