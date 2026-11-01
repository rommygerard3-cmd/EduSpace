@extends('layouts.app')

@section('title', 'Finances')

@section('content')
    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="card"><div class="card-body"><p class="stat-label">Frais attendus</p><p class="stat-value mt-2">{{ number_format($expected_fees, 2, ',', ' ') }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Facture</p><p class="stat-value mt-2">{{ number_format($invoiced, 2, ',', ' ') }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Encaisse</p><p class="stat-value mt-2 text-emerald-700">{{ number_format($paid, 2, ',', ' ') }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Solde restant</p><p class="stat-value mt-2 text-red-700">{{ number_format($balance, 2, ',', ' ') }}</p></div></div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="mb-4 flex items-center justify-between">
                    <h2>Paiements recents</h2>
                    <a href="{{ route('finance.invoices.index') }}" class="text-sm font-semibold text-blue-700">Factures</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr><th>Eleve</th><th>Montant</th><th>Date</th><th></th></tr>
                        </thead>
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
    </div>
@endsection
