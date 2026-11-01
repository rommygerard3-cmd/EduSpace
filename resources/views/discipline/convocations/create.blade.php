@extends('layouts.app')

@section('title', 'Discipline - Nouvelle convocation')

@section('content')
    <div class="card">
        <div class="card-body">
            <h2 class="mb-6 text-lg font-semibold">Creer une convocation</h2>
            <form method="POST" action="{{ route('discipline.convocations.store') }}" class="max-w-2xl space-y-6">
                @csrf

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Eleve</label>
                    <select name="student_id" class="input" required>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->fullName() }} ({{ $student->matricule }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Destinataire</label>
                    <select name="recipient" class="input" required>
                        <option value="parent">Parent / responsable</option>
                        <option value="student">Eleve</option>
                        <option value="both">Parent et Eleve</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Motif</label>
                    <textarea name="reason" class="input" rows="3" required></textarea>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Date et heure du rendez-vous</label>
                    <input type="datetime-local" name="meeting_at" class="input" required>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Notes</label>
                    <textarea name="notes" class="input" rows="3"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('discipline.convocations.index') }}" class="btn-secondary">Annuler</a>
                    <button type="submit" class="btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
@endsection
