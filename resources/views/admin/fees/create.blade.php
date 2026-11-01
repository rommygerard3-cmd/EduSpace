@extends('layouts.app')

@section('title', 'Ajouter des frais')

@section('content')
    <div class="grid gap-6 xl:grid-cols-2">
        <div class="card">
            <div class="card-body">
                <h2 class="mb-1">Frais pour un élève</h2>
                <p class="mb-5 text-sm text-slate-500">Année active : {{ $year->name }}</p>
                <form method="POST" action="{{ route('admin.fees.store') }}" class="space-y-4">
                    @csrf
                    <div><label class="label" for="student_id">Élève</label><select class="input" id="student_id" name="student_id" required><option value="">Sélectionner</option>@foreach($students as $student)<option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>{{ $student->fullName() }} — {{ $student->matricule }}</option>@endforeach</select></div>
                    <div><label class="label" for="fee_category_id">Catégorie</label><select class="input" id="fee_category_id" name="fee_category_id" required><option value="">Sélectionner</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('fee_category_id') == $category->id)>{{ $category->name }} — {{ number_format((float) $category->amount, 2, ',', ' ') }}</option>@endforeach</select></div>
                    <div><label class="label" for="amount">Montant</label><input class="input" type="number" step="0.01" min="0" id="amount" name="amount" value="{{ old('amount') }}" required></div>
                    <div><label class="label" for="due_date">Échéance</label><input class="input" type="date" id="due_date" name="due_date" value="{{ old('due_date') }}"></div>
                    <button class="btn-primary w-full">Enregistrer le frais</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="mb-1">Affectation en masse</h2>
                <p class="mb-5 text-sm text-slate-500">Ajoute le frais à tous les élèves inscrits d’une classe. Les élèves ayant déjà une facture sont ignorés.</p>
                <form method="POST" action="{{ route('admin.fees.bulk') }}" class="space-y-4">
                    @csrf
                    <div><label class="label" for="bulk_class">Classe</label><select class="input" id="bulk_class" name="school_class_id" required><option value="">Sélectionner</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(old('school_class_id') == $class->id)>{{ $class->displayName() }}</option>@endforeach</select></div>
                    <div><label class="label" for="bulk_category">Catégorie</label><select class="input" id="bulk_category" name="fee_category_id" required><option value="">Sélectionner</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('fee_category_id') == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
                    <div><label class="label" for="bulk_amount">Montant</label><input class="input" type="number" step="0.01" min="0" id="bulk_amount" name="amount" value="{{ old('amount') }}" required></div>
                    <div><label class="label" for="bulk_due_date">Échéance</label><input class="input" type="date" id="bulk_due_date" name="due_date" value="{{ old('due_date') }}"></div>
                    <button class="btn-primary w-full">Affecter à la classe</button>
                </form>
            </div>
        </div>
    </div>
@endsection
