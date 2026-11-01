@extends('layouts.app')

@section('title', 'Factures')

@section('content')
    <div class="space-y-5">
        <form method="GET" class="flex flex-wrap gap-2">
            <input class="input sm:max-w-sm" type="search" name="q" value="{{ request('q') }}" placeholder="Numéro, élève ou matricule">
            <select class="input" name="status">
                <option value="">Tous les statuts</option>
                <option value="issued" @selected(request('status') === 'issued')>Émise</option>
                <option value="partial" @selected(request('status') === 'partial')>Partiellement payée</option>
                <option value="paid" @selected(request('status') === 'paid')>Payée</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>Annulée</option>
            </select>
            <button class="btn-secondary">Filtrer</button>
        </form>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Facture</th><th>Élève</th><th>Total</th><th>Payé</th><th>Solde</th><th>Statut</th><th></th></tr></thead>
                    <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td><p class="font-mono text-xs">{{ $invoice->number }}</p><p class="text-xs text-slate-500">{{ $invoice->issued_at?->format('d/m/Y') }}</p></td>
                            <td>{{ $invoice->student?->fullName() }}</td>
                            <td>{{ number_format((float) $invoice->total, 2, ',', ' ') }}</td>
                            <td>{{ number_format($invoice->paidTotal(), 2, ',', ' ') }}</td>
                            <td class="font-semibold {{ $invoice->balance() > 0 ? 'text-red-700' : 'text-emerald-700' }}">{{ number_format($invoice->balance(), 2, ',', ' ') }}</td>
                            <td><span class="badge-{{ $invoice->status === 'paid' ? 'green' : ($invoice->status === 'partial' ? 'amber' : ($invoice->status === 'cancelled' ? 'red' : 'slate')) }}">{{ ucfirst($invoice->status) }}</span></td>
                            <td class="text-right"><a href="{{ route('finance.invoices.show', $invoice) }}" class="text-sm font-semibold text-blue-700">Détail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-slate-500">Aucune facture.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{ $invoices->links() }}
    </div>
@endsection
