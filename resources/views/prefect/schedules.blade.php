@extends('layouts.app')

@section('title', 'Horaires')

@section('content')
    <div class="space-y-5">
        <form method="GET" class="flex flex-wrap gap-2">
            <select class="input" name="class">
                <option value="">Toutes les classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" @selected($selectedClass?->id === $class->id)>{{ $class->displayName() }}</option>
                @endforeach
            </select>
            <select class="input" name="teacher">
                <option value="">Tous les enseignants</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" @selected($selectedTeacher?->id === $teacher->id)>{{ $teacher->fullName() }}</option>
                @endforeach
            </select>
            <button class="btn-secondary">Filtrer</button>
        </form>
        @include('components.schedule-week', ['schedules' => $schedules])
    </div>
@endsection
