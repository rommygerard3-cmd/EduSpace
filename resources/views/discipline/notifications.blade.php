@extends('layouts.app')

@section('title', 'Discipline - Notifications')

@section('content')
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card">
            <div class="card-body">
                <h2 class="mb-4 text-lg font-semibold">Convocations</h2>
                <div class="space-y-2 text-sm">
                    @forelse($convocations as $convocation)
                        <a href="{{ route('discipline.convocations.edit', $convocation) }}" class="block rounded-lg bg-slate-50 px-3 py-2 hover:bg-slate-100">
                            <span class="font-medium">{{ $convocation->student?->fullName() }}</span>
                            <span class="ml-2 text-xs text-slate-500">{{ $convocation->meeting_at?->format('d/m/Y H:i') }}</span>
                            <span class="ml-2 text-xs font-semibold uppercase text-amber-600">{{ $convocation->status }}</span>
                        </a>
                    @empty
                        <p class="text-slate-500">Aucune convocation.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="mb-4 text-lg font-semibold">Incidents</h2>
                <div class="space-y-2 text-sm">
                    @forelse($incidents as $incident)
                        <a href="{{ route('discipline.incidents.edit', $incident) }}" class="block rounded-lg bg-slate-50 px-3 py-2 hover:bg-slate-100">
                            <span class="font-medium">{{ $incident->student?->fullName() }}</span>
                            <span class="ml-2 text-xs text-slate-500">{{ $incident->occurred_at?->format('d/m/Y') }}</span>
                            <span class="ml-2 text-xs font-semibold uppercase text-red-600">{{ $incident->status }}</span>
                        </a>
                    @empty
                        <p class="text-slate-500">Aucun incident.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card lg:col-span-2">
            <div class="card-body">
                <h2 class="mb-4 text-lg font-semibold">Notifications internes</h2>
                <div class="space-y-2 text-sm">
                    @forelse($notifications as $notification)
                        <div class="rounded-lg bg-slate-50 px-3 py-2">
                            <p class="font-medium">{{ $notification->title }}</p>
                            <p class="text-sm text-slate-500">{{ $notification->body }}</p>
                            <p class="text-xs text-slate-400">{{ $notification->created_at?->format('d/m/Y H:i') }}</p>
                        </div>
                    @empty
                        <p class="text-slate-500">Aucune notification.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
