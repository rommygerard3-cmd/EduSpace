@extends('layouts.app')

@section('title', 'Enseignement - Fiche de preparation')

@section('content')
    <div class="card">
        <div class="card-body">
            <h2 class="mb-6 text-lg font-semibold">Modifier la fiche</h2>
            <form method="POST" action="{{ route('teacher.preparations.update', $preparation) }}" class="max-w-4xl space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Affectation (classe / matiere)</label>
                    <select name="class_subject_id" class="input" required>
                        @foreach($assignments as $assignment)
                            <option value="{{ $assignment->id }}" @selected($assignment->id == $preparation->class_subject_id)>{{ optional($assignment->schoolClass)->displayName() }} - {{ optional($assignment->subject)->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Trimestre</label>
                    <select name="term_id" class="input" required>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" @selected($term->id == $preparation->term_id)>{{ optional($term->academicYear)->name }} - {{ $term->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Date</label>
                    <input type="date" name="date" class="input" value="{{ $preparation->date ? $preparation->date->toDateString() : '' }}" required>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Sujet</label>
                    <input type="text" name="topic" class="input" value="{{ $preparation->topic }}" required maxlength="255">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Objectifs pedagogiques</label>
                    <textarea name="objectives" class="input" rows="4" required>{{ $preparation->objectives }}</textarea>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Prerequis</label>
                    <textarea name="prerequisites" class="input" rows="3">{{ $preparation->prerequisites }}</textarea>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Materiel pedagogique</label>
                    <textarea name="materials" class="input" rows="3">{{ $preparation->materials }}</textarea>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Deroulement de la lecon</label>
                    <textarea name="steps" class="input" rows="6" required>{{ $preparation->steps }}</textarea>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Methodes d'evaluation</label>
                    <textarea name="evaluation_methods" class="input" rows="3">{{ $preparation->evaluation_methods }}</textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('teacher.preparations.index') }}" class="btn-secondary">Annuler</a>
                    <button type="submit" class="btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
@endsection
