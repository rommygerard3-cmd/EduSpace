@extends('layouts.app')

@section('title', 'Bulletins')

@section('content')
    <div class="space-y-4">
        @forelse($reports as $data)
            <div class="card">
                <div class="card-body flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2>{{ $data['term']->name }}</h2>
                        <p class="text-sm text-slate-500">Moyenne : {{ $data['average'] !== null ? number_format($data['average'], 2) : '—' }} · {{ $data['decision'] }}</p>
                        @if(! $data['published'])
                            <p class="mt-1 text-xs text-amber-700">Bulletin non encore publié par la Direction.</p>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        @if($data['published'])
                            <a href="{{ route('student.report-cards.show', $data['term']) }}" class="btn-secondary btn-sm">Consulter</a>
                            <a href="{{ route('student.report-cards.pdf', $data['term']) }}" class="btn-primary btn-sm">Télécharger le PDF</a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="card"><div class="card-body text-sm text-slate-500">Aucun bulletin n’est disponible.</div></div>
        @endforelse
    </div>
@endsection
