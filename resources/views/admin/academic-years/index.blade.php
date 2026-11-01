@extends('layouts.app')

@section('title', 'Années scolaires')

@section('content')
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="card xl:col-span-2">
            <div class="card-body">
                <h2 class="mb-4">Années scolaires</h2>
                <div class="space-y-3">
                    @forelse($years as $year)
                        <div class="rounded-xl border border-slate-200 p-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-lg font-semibold text-slate-900">{{ $year->name }}</p>
                                        @if($year->is_current)<span class="badge-green">Active</span>@endif
                                    </div>
                                    <p class="text-sm text-slate-500">{{ $year->start_date?->format('d/m/Y') }} → {{ $year->end_date?->format('d/m/Y') }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $year->schoolClasses->count() }} classe(s) · {{ $year->enrollments_count }} inscription(s) · {{ $year->terms->count() }} trimestre(s)</p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @if(! $year->is_current)
                                        <form method="POST" action="{{ route('admin.academic-years.current', $year) }}">@csrf<button class="btn-secondary btn-sm">Activer</button></form>
                                    @endif
                                    @if($year->schoolClasses->isEmpty() && $year->enrollments_count === 0)
                                        <form method="POST" action="{{ route('admin.academic-years.destroy', $year) }}" data-confirm="Supprimer cette année scolaire ?">@csrf @method('DELETE')<button class="btn-danger btn-sm">Supprimer</button></form>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach($year->terms->sortBy('sequence') as $term)
                                    <span class="badge-{{ $term->is_current ? 'blue' : 'slate' }}">{{ $term->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Aucune année scolaire.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="mb-4">Nouvelle année</h2>
                <form method="POST" action="{{ route('admin.academic-years.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="label" for="name">Nom</label>
                        <input class="input" id="name" name="name" value="{{ old('name') }}" placeholder="2026-2027" required>
                    </div>
                    <div>
                        <label class="label" for="start_date">Date de début</label>
                        <input class="input" type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                    </div>
                    <div>
                        <label class="label" for="end_date">Date de fin</label>
                        <input class="input" type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-700"><input type="checkbox" name="is_current" value="1" class="rounded border-slate-300 text-blue-700" @checked(old('is_current'))> Définir comme année active</label>
                    <button class="btn-primary w-full">Créer l’année</button>
                    <p class="text-xs text-slate-500">Trois trimestres sont créés automatiquement.</p>
                </form>
            </div>
        </div>
    </div>
@endsection
