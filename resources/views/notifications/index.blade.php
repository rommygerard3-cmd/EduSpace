@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-4 flex items-center justify-between">
            <p class="text-sm text-slate-500">{{ $notifications->total() }} notification(s)</p>
            @if($notifications->contains(fn ($notification) => ! $notification->read_at))
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button class="btn-secondary btn-sm">Tout marquer comme lu</button>
                </form>
            @endif
        </div>

        <div class="space-y-3">
            @forelse($notifications as $notification)
                <div class="card {{ $notification->read_at ? 'opacity-75' : 'border-l-4 border-l-blue-600' }}">
                    <div class="card-body flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $notification->title }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ $notification->body }}</p>
                            <p class="mt-2 text-xs text-slate-400">{{ $notification->created_at->format('d/m/Y H:i') }} · {{ $notification->channel }}</p>
                        </div>
                        @if(! $notification->read_at)
                            <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                @csrf
                                <button class="btn-secondary btn-sm">Marquer comme lu</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="card"><div class="card-body text-center text-sm text-slate-500">Aucune notification.</div></div>
            @endforelse
        </div>

        <div class="mt-6">{{ $notifications->links() }}</div>
    </div>
@endsection
