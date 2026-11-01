@extends('layouts.app')

@section('title', 'Frais scolaires')

@section('content')
    <div class="space-y-5">
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="card"><div class="card-body"><p class="stat-label">Total des frais</p><p class="stat-value mt-2">{{ number_format($total, 2, ',', ' ') }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Année active</p><p class="mt-2 text-lg font-semibold">{{ $year?->name ?? 'Aucune' }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Catégories</p><p class="mt-2 text-lg font-semibold">{{ $categories->count() }}</p><a href="{{ route('admin.finance-resources.index', 'fee-categories') }}" class="mt-1 inline-block text-sm font-semibold text-blue-700">Gérer les catégories</a></div></div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" class="flex flex-wrap gap-2">
                <input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Élève ou matricule">
                <select class="input" name="class">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $class)<option value="{{ $class->id }}" @selected(request('class') == $class->id)>{{ $class->displayName() }}</option>@endforeach
                </select>
                <select class="input" name="category">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>@endforeach
                </select>
                <button class="btn-secondary">Filtrer</button>
            </form>
            <a href="{{ route('admin.fees.create') }}" class="btn-primary">Ajouter des frais</a>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Élève</th><th>Catégorie</th><th>Montant</th><th>Échéance</th><th>Statut</th><th></th></tr></thead>
                    <tbody>
                    @forelse($fees as $fee)
                        <tr>
                            <td><p class="font-medium">{{ $fee->student?->fullName() }}</p><p class="font-mono text-xs text-slate-500">{{ $fee->student?->matricule }}</p></td>
                            <td>{{ $fee->category?->name ?? '—' }}</td>
                            <td class="font-semibold">{{ number_format((float) $fee->amount, 2, ',', ' ') }}</td>
                            <td>{{ $fee->due_date?->format('d/m/Y') ?? '—' }}</td>
                            <td><span class="badge-{{ $fee->status === 'paid' ? 'green' : ($fee->status === 'pending' ? 'amber' : 'slate') }}">{{ $fee->status === 'paid' ? 'Payé' : ($fee->status === 'pending' ? 'En attente' : $fee->status) }}</span></td>
                            <td class="text-right"><form method="POST" action="{{ route('admin.fees.destroy', $fee) }}" data-confirm="Supprimer ce frais ?">@csrf @method('DELETE')<button class="text-sm font-semibold text-red-600">Supprimer</button></form></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-slate-500">Aucun frais enregistré.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{ $fees->links() }}
    </div>
@endsection
