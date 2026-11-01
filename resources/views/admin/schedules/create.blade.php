@extends('layouts.app')

@section('title', 'Ajouter un cours')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="card">
            <div class="card-body">
                <h2 class="mb-1">Nouveau créneau</h2>
                <p class="mb-5 text-sm text-slate-500">Année active : {{ $year->name }}. La matière doit déjà être affectée à la classe et à l’enseignant.</p>
                <form method="POST" action="{{ route('admin.schedules.store') }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    <div class="sm:col-span-2">
                        <label class="label" for="school_class_id">Classe</label>
                        <select class="input" id="school_class_id" name="school_class_id" required>
                            <option value="">Sélectionner</option>
                            @foreach($classes as $class)<option value="{{ $class->id }}" @selected(old('school_class_id') == $class->id)>{{ $class->displayName() }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label" for="subject_id">Matière</label>
                        <select class="input" id="subject_id" name="subject_id" required>
                            <option value="">Sélectionner</option>
                            @foreach($subjects as $subject)<option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->name }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label" for="teacher_id">Enseignant</label>
                        <select class="input" id="teacher_id" name="teacher_id" required>
                            <option value="">Sélectionner</option>
                            @foreach($teachers as $teacher)<option value="{{ $teacher->id }}" @selected(old('teacher_id') == $teacher->id)>{{ $teacher->fullName() }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label" for="day_of_week">Jour</label>
                        <select class="input" id="day_of_week" name="day_of_week" required>
                            @foreach($days as $number => $label)<option value="{{ $number }}" @selected(old('day_of_week') == $number)>{{ $label }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label" for="room">Local</label>
                        <input class="input" id="room" name="room" value="{{ old('room') }}">
                    </div>
                    <div>
                        <label class="label" for="start_time">Début</label>
                        <input class="input" type="time" id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                    </div>
                    <div>
                        <label class="label" for="end_time">Fin</label>
                        <input class="input" type="time" id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                    </div>
                    <div class="flex gap-2 sm:col-span-2">
                        <button class="btn-primary">Ajouter le cours</button>
                        <a href="{{ route('admin.schedules.index') }}" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
