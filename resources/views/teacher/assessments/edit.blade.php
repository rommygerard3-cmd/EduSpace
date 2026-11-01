@extends('layouts.app')

@section('title', 'Modifier une évaluation')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="card">
            <div class="card-body">
                <h2 class="mb-5 text-xl font-bold text-slate-900">Modifier {{ $assessment->title }}</h2>
                <form method="POST" action="{{ route('teacher.assessments.update', $assessment) }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    @method('PUT')
                    <div class="sm:col-span-2">
                        <label class="label" for="class_subject_id">Classe et matière</label>
                        <select class="input" id="class_subject_id" name="class_subject_id" required>
                            <option value="">Sélectionner</option>
                            @foreach($assignments as $assignment)
                                <option value="{{ $assignment->id }}" @selected(old('class_subject_id', $assessment->school_class_id) == $assignment->id)>{{ $assignment->schoolClass?->displayName() }} — {{ $assignment->subject?->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label" for="term_id">Trimestre</label>
                        <select class="input" id="term_id" name="term_id" required>
                            <option value="">Sélectionner</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" @selected(old('term_id', $assessment->term_id) == $term->id)>{{ $term->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label" for="type">Type</label>
                        <select class="input" id="type" name="type" required>
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}" @selected(old('type', $assessment->type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label" for="title">Titre</label>
                        <input class="input" id="title" name="title" value="{{ old('title', $assessment->title) }}" required>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label" for="description">Description</label>
                        <textarea class="input" id="description" name="description" rows="3">{{ old('description', $assessment->description) }}</textarea>
                    </div>
                    <div>
                        <label class="label" for="date">Date</label>
                        <input class="input" type="date" id="date" name="date" value="{{ old('date', $assessment->date?->format('Y-m-d')) }}" required>
                    </div>
                    <div>
                        <label class="label" for="max_score">Note maximale</label>
                        <input class="input" type="number" step="0.01" min="1" max="100" id="max_score" name="max_score" value="{{ old('max_score', $assessment->max_score) }}" required>
                    </div>
                    <div>
                        <label class="label" for="coefficient">Coefficient</label>
                        <input class="input" type="number" step="0.5" min="0.5" max="10" id="coefficient" name="coefficient" value="{{ old('coefficient', $assessment->coefficient) }}" required>
                    </div>
                    <div class="flex flex-wrap gap-2 sm:col-span-2">
                        <button class="btn-primary" type="submit">Enregistrer</button>
                        @if($assessment->status === 'draft')
                            <form method="POST" action="{{ route('teacher.assessments.publish', $assessment) }}" class="inline">
                                @csrf
                                <button class="btn-secondary" type="submit">Publier</button>
                            </form>
                        @endif
                        <a href="{{ route('teacher.assessments.index') }}" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
