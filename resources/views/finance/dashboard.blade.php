@extends('layouts.app')

@section('title', 'Finances')

@section('content')
    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="card"><div class="card-body"><p class="stat-label">Frais attendus</p><p class="stat-value mt-2">{{ number_format($expected_fees, 2, ',', ' ') }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Facturé</p><p class="stat-value mt-2">{{ number_format($invoiced, 2, ',', ' ') }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Encaissé</p><p class="stat-value mt-2 text-emerald-700">{{ number_format($paid, 2, ',', ' ') }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Solde à recouvrer</p><p class="stat-value mt-2 text-red-700">{{ number_format($balance, 2, ',', ' ') }}</p></div></div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4 flex items-center justify-between"><h2>Paiements récents</h2><a href="{{ route('finance.invoices.index') }}" class="text-sm font-semibold text-blue-700">Factures</a></div>
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead><tr><th>Élève</th><th>Montant</th><th>Date</th><th></th></tr></thead>
                            <tbody>
                            @forelse($recentPayments as $payment)
                                <tr>
                                    <td>{{ $payment->student?->fullName() }}</td>
                                    <td class="font-semibold">{{ number_format((float) $payment->amount, 2, ',', ' ') }}</td>
                                    <td class="text-xs">{{ $payment->paid_at?->format('d/m/Y H:i') }}</td>
                                    <td class="text-right"><a href="{{ route('finance.invoices.show', $payment->invoice_id) }}" class="text-sm font-semibold text-blue-700">Voir</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-6 text-center text-slate-500">Aucun paiement.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="mb-4">Factures à recouvrer</h2>
                    <div class="space-y-2 text-sm">
                        @forelse($unpaidInvoices as $invoice)
                            <a href="{{ route('finance.invoices.show', $invoice) }}" class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2 hover:bg-slate-100">
                                <span>{{ $invoice->student?->fullName() }} <span class="font-mono text-xs text-slate-500">{{ $invoice->number }}</span></span>
                                <span class="font-semibold text-red-700">{{ number_format($invoice->balance(), 2, ',', ' ') }}</span>
                            </a>
                        @empty
                            <p class="text-slate-500">Aucune facture en attente.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
