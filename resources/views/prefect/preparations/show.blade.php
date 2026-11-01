@extends('layouts.app')

@section('title', 'Examen de fiche')

@section('content')
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-4 xl:col-span-2">
            <div class="card"><div class="card-body">
                <p class="text-sm text-slate-500">{{ $preparation->teacher?->fullName() }} · {{ $preparation->schoolClass?->displayName() }} · {{ $preparation->subject?->name }}</p>
                <h2 class="mt-1">{{ $preparation->topic }}</h2>
                <p class="text-sm text-slate-500">{{ $preparation->date?->format('d/m/Y') }} · {{ $preparation->term?->name }}</p>
            </div></div>
            @foreach(['objectives' => 'Objectifs', 'prerequisites' => 'Prérequis', 'materials' => 'Matériel', 'steps' => 'Déroulement', 'evaluation_methods' => 'Évaluation'] as $field => $label)
                <div class="card"><div class="card-body">
                    <h2 class="mb-2">{{ $label }}</h2>
                    <p class="whitespace-pre-wrap text-sm text-slate-700">{{ $preparation->{$field} ?: '—' }}</p>
                </div></div>
            @endforeach
        </div>
        <div class="card h-fit">
            <div class="card-body">
                <h2 class="mb-4">Décision</h2>
                <form method="POST" action="{{ route('prefect.preparations.review', $preparation) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="label" for="decision">Décision</label>
                        <select class="input" id="decision" name="decision" required>
                            <option value="approved">Approuver</option>
                            <option value="revision">Demander une correction</option>
                            <option value="reviewed">Marquer comme examinée</option>
                        </select>
                    </div>
                    <div>
                        <label class="label" for="review_notes">Observations</label>
                        <textarea class="input" id="review_notes" name="review_notes" rows="5">{{ old('review_notes', $preparation->review_notes) }}</textarea>
                    </div>
                    <button class="btn-primary w-full">Enregistrer la décision</button>
                </form>
            </div>
        </div>
    </div>
@endsection
