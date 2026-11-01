@extends('layouts.app')

@section('title', 'Mon horaire')

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Mon horaire</h2>
            <p class="text-sm text-slate-500">Année scolaire en cours.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach(\App\Models\Schedule::DAYS as $dayNumber => $dayLabel)
                <div class="card">
                    <div class="card-body">
                        <h3 class="mb-3 text-base font-semibold text-slate-900">{{ $dayLabel }}</h3>
                        <div class="space-y-2">
                            @forelse($schedules->get($dayNumber, collect()) as $schedule)
                                <div class="rounded-lg border border-slate-200 p-3 text-sm">
                                    <p class="font-semibold text-slate-900">{{ substr($schedule->start_time, 0, 5) }}–{{ substr($schedule->end_time, 0, 5) }}</p>
                                    <p class="text-slate-600">{{ $schedule->subject?->name }} · {{ $schedule->schoolClass?->displayName() }}</p>
                                    <p class="text-xs text-slate-500">{{ $schedule->room ?: 'Sans local' }}</p>
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
