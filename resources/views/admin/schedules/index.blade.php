@extends('layouts.app')

@section('title', 'Emplois du temps')

@section('content')
    <div class="space-y-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
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
            <a href="{{ route('admin.schedules.create') }}" class="btn-primary">Ajouter un cours</a>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach(\App\Models\Schedule::DAYS as $dayNumber => $dayLabel)
                <div class="card">
                    <div class="card-body">
                        <h2 class="mb-3 text-base">{{ $dayLabel }}</h2>
                        <div class="space-y-2">
                            @forelse($schedules->get($dayNumber, collect()) as $schedule)
                                <div class="rounded-lg border border-slate-200 p-3 text-sm">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ substr($schedule->start_time, 0, 5) }}–{{ substr($schedule->end_time, 0, 5) }}</p>
                                            <p class="text-slate-600">{{ $schedule->subject?->name }} · {{ $schedule->schoolClass?->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $schedule->teacher?->fullName() }}{{ $schedule->room ? ' · '.$schedule->room : '' }}</p>
                                        </div>
                                        <form method="POST" action="{{ route('admin.schedules.destroy', $schedule) }}" data-confirm="Retirer ce cours ?">@csrf @method('DELETE')<button class="text-xs font-semibold text-red-600">Retirer</button></form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-400">Aucun cours.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
