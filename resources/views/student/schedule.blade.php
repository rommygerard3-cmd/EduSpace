@extends('layouts.app')

@section('title', 'Emploi du temps')

@section('content')
    <div class="space-y-5">
        <div>
            <h2 class="text-xl font-bold">Emploi du temps</h2>
            <p class="text-sm text-slate-500">{{ $class?->displayName() ?? 'Aucune classe associée à votre inscription.' }}</p>
        </div>
        @if($class)
            @include('components.schedule-week', ['schedules' => $schedules])
        @else
            <div class="card"><div class="card-body text-sm text-slate-500">Vous n’êtes pas encore inscrit dans une classe pour l’année active.</div></div>
        @endif
    </div>
@endsection
