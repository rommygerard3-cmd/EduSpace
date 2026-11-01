@extends('layouts.app')

@section('title', 'Notes')

@section('content')
    <div class="space-y-5">
        <form method="GET" class="flex flex-wrap gap-2">
            <select class="input w-auto" name="term" onchange="this.form.submit()">
                @foreach($terms as $term)
                    <option value="{{ $term->id }}" @selected($selectedTerm?->id === $term->id)>{{ $term->name }}</option>
                @endforeach
            </select>
        </form>

        @if(! $data)
            <div class="card"><div class="card-body text-sm text-slate-500">Aucune période scolaire n’est disponible.</div></div>
        @else
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="card"><div class="card-body"><p class="stat-label">Moyenne</p><p class="stat-value mt-2">{{ $data['average'] !== null ? number_format($data['average'], 2) : '—' }}</p></div></div>
                <div class="card"><div class="card-body"><p class="stat-label">Mention</p><p class="stat-value mt-2 text-lg">{{ $data['mention'] }}</p></div></div>
                <div class="card"><div class="card-body"><p class="stat-label">Rang</p><p class="stat-value mt-2">{{ $data['rank'] ?? '—' }}</p></div></div>
            </div>
            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead><tr><th>Matière</th><th>Notes publiées</th><th>Moyenne /20</th><th>Coef.</th></tr></thead>
                        <tbody>
                        @forelse($data['subjects'] as $subject)
                            <tr>
                                <td class="font-medium">{{ $subject['subject']?->name }}</td>
                                <td class="text-sm">
                                    @forelse($subject['grades'] as $grade)
                                        <span class="mr-2">{{ $grade->assessment?->title }} : {{ $grade->score }}/{{ $grade->assessment?->max_score }}</span>
                                    @empty
                                        —
                                    @endforelse
                                </td>
                                <td>{{ $subject['average'] !== null ? number_format($subject['average'], 2) : '—' }}</td>
                                <td>{{ $subject['coefficient'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-8 text-center text-slate-500">Aucune note publiée pour cette période.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
