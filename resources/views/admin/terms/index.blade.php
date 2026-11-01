@extends('layouts.app')

@section('title', 'Trimestres')

@section('content')
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-5 xl:col-span-2">
            @forelse($years as $year)
                <div class="card">
                    <div class="card-body">
                        <div class="mb-4 flex items-center justify-between">
                            <h2>{{ $year->name }}</h2>
                            @if($year->is_current)<span class="badge-green">Année active</span>@endif
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead><tr><th>Trimestre</th><th>Période</th><th>État</th><th></th></tr></thead>
                                <tbody>
                                @forelse($year->terms as $term)
                                    <tr>
                                        <td class="font-medium">{{ $term->name }} <span class="text-xs text-slate-400">#{{ $term->sequence }}</span></td>
                                        <td class="text-sm">{{ $term->start_date?->format('d/m/Y') }} → {{ $term->end_date?->format('d/m/Y') }}</td>
                                        <td>
                                            @if($term->is_current)
                                                <span class="badge-blue">En cours</span>
                                            @else
                                                <span class="badge-slate">Inactif</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <details class="inline-block text-left">
                                                <summary class="cursor-pointer text-sm font-semibold text-blue-700">Modifier</summary>
                                                <form method="POST" action="{{ route('admin.terms.update', $term) }}" class="mt-3 w-72 space-y-3 rounded-lg border border-slate-200 bg-white p-3 shadow-lg">
                                                    @csrf
                                                    @method('PUT')
                                                    <div><label class="label">Nom</label><input class="input" name="name" value="{{ $term->name }}" required></div>
                                                    <div><label class="label">Séquence</label><input class="input" type="number" name="sequence" value="{{ $term->sequence }}" min="1" max="10" required></div>
                                                    <div><label class="label">Début</label><input class="input" type="date" name="start_date" value="{{ $term->start_date?->format('Y-m-d') }}" required></div>
                                                    <div><label class="label">Fin</label><input class="input" type="date" name="end_date" value="{{ $term->end_date?->format('Y-m-d') }}" required></div>
                                                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_current" value="1" @checked($term->is_current)> Trimestre en cours</label>
                                                    <div class="flex gap-2">
                                                        <button class="btn-primary btn-sm">Enregistrer</button>
                                                        <button class="btn-danger btn-sm" form="delete-term-{{ $term->id }}">Supprimer</button>
                                                    </div>
                                                </form>
                                            </details>
                                            <form id="delete-term-{{ $term->id }}" method="POST" action="{{ route('admin.terms.destroy', $term) }}" data-confirm="Supprimer ce trimestre ?">@csrf @method('DELETE')</form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-5 text-center text-slate-500">Aucun trimestre.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card"><div class="card-body text-sm text-slate-500">Créez d’abord une année scolaire.</div></div>
            @endforelse
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="mb-4">Nouveau trimestre</h2>
                <form method="POST" action="{{ route('admin.terms.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="label" for="academic_year_id">Année scolaire</label>
                        <select class="input" id="academic_year_id" name="academic_year_id" required>
                            @foreach($years as $year)
                                <option value="{{ $year->id }}" @selected(old('academic_year_id', $year->is_current ? $year->id : null) == $year->id)>{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label" for="term_name">Nom</label>
                        <input class="input" id="term_name" name="name" value="{{ old('name') }}" placeholder="1er trimestre" required>
                    </div>
                    <div>
                        <label class="label" for="sequence">Séquence</label>
                        <input class="input" type="number" id="sequence" name="sequence" value="{{ old('sequence', 1) }}" min="1" max="10" required>
                    </div>
                    <div>
                        <label class="label" for="term_start">Début</label>
                        <input class="input" type="date" id="term_start" name="start_date" value="{{ old('start_date') }}" required>
                    </div>
                    <div>
                        <label class="label" for="term_end">Fin</label>
                        <input class="input" type="date" id="term_end" name="end_date" value="{{ old('end_date') }}" required>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-700"><input type="checkbox" name="is_current" value="1" @checked(old('is_current'))> Définir comme trimestre en cours</label>
                    <button class="btn-primary w-full">Créer le trimestre</button>
                </form>
            </div>
        </div>
    </div>
@endsection
