@extends('layouts.app')

@section('title', 'Enseignement - Nouvelle fiche')

@section('content')
    <div class="card">
        <div class="card-body">
            <h2 class="mb-6 text-lg font-semibold">Creer une fiche de preparation</h2>
            <form method="POST" action="{{ route('teacher.preparations.store') }}" class="max-w-4xl space-y-6">
                @csrf

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Affectation (classe / matiere)</label>
                    <select name="class_subject_id" class="input" required>
                        @foreach($assignments as $assignment)
                            <option value="{{ $assignment->id }}">{{ optional($assignment->schoolClass)->displayName() }} - {{ optional($assignment->subject)->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Trimestre</label>
                    <select name="term_id" class="input" required>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}">{{ optional($term->academicYear)->name }} - {{ $term->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Date</label>
                    <input type="date" name="date" class="input" required>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Sujet</label>
                    <input type="text" name="topic" class="input" required maxlength="255">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Objectifs pedagogiques</label>
                    <textarea name="objectives" class="input" rows="4" required></textarea>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Prerequis</label>
                    <textarea name="prerequisites" class="input" rows="3"></textarea>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Materiel pedagogique</label>
                    <textarea name="materials" class="input" rows="3"></textarea>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Deroulement de la lecon</label>
                    <textarea name="steps" class="input" rows="6" required></textarea>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Methodes d'evaluation</label>
                    <textarea name="evaluation_methods" class="input" rows="3"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('teacher.preparations.index') }}" class="btn-secondary">Annuler</a>
                    <button type="submit" class="btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
@endsection
