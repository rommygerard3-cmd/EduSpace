@props(['schedules'])
<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    @foreach(\App\Models\Schedule::DAYS as $dayNumber => $dayLabel)
        <div class="card">
            <div class="card-body">
                <h2 class="mb-3 text-base">{{ $dayLabel }}</h2>
                <div class="space-y-2">
                    @forelse($schedules->get($dayNumber, collect()) as $schedule)
                        <div class="rounded-lg border border-slate-200 p-3 text-sm">
                            <p class="font-semibold text-slate-900">{{ substr((string) $schedule->start_time, 0, 5) }}–{{ substr((string) $schedule->end_time, 0, 5) }}</p>
                            <p class="text-slate-600">{{ $schedule->subject?->name }} · {{ $schedule->schoolClass?->name }}</p>
                            <p class="text-xs text-slate-500">{{ $schedule->teacher?->fullName() }}{{ $schedule->room ? ' · '.$schedule->room : '' }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Aucun cours.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endforeach
</div>
