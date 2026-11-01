@extends('layouts.app')

@section('title', 'Paiements')

@section('content')
    <div class="space-y-4">
        @forelse($invoices as $invoice)
            <div class="card">
                <div class="card-body">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="font-mono text-xs text-slate-500">{{ $invoice->number }}</p>
                            <h2>{{ $invoice->academicYear?->name }}</h2>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-slate-500">Total {{ number_format((float) $invoice->total, 2, ',', ' ') }}</p>
                            <p class="font-bold {{ $invoice->balance() > 0 ? 'text-red-700' : 'text-emerald-700' }}">Solde {{ number_format($invoice->balance(), 2, ',', ' ') }}</p>
                        </div>
                    </div>
                    <div class="mt-4 overflow-x-auto">
                        <table class="table">
                            <thead><tr><th>Frais</th><th>Montant</th></tr></thead>
                            <tbody>
                            @foreach($invoice->items as $item)
                                <tr><td>{{ $item->description }}</td><td>{{ number_format((float) $item->amount, 2, ',', ' ') }}</td></tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <h3 class="mt-4 text-sm font-semibold">Historique des paiements</h3>
                    <div class="mt-2 space-y-2 text-sm">
                        @forelse($invoice->payments as $payment)
                            <div class="flex justify-between rounded-lg bg-slate-50 px-3 py-2">
                                <span>{{ $payment->paid_at?->format('d/m/Y') }} · {{ $payment->status === 'valid' ? 'Valide' : 'Annulé' }}</span>
                                <span class="font-semibold">{{ number_format((float) $payment->amount, 2, ',', ' ') }}</span>
                            </div>
                        @empty
                            <p class="text-slate-500">Aucun paiement enregistré.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @empty
            <div class="card"><div class="card-body text-sm text-slate-500">Aucune facture n’est associée à votre compte pour l’année active.</div></div>
        @endforelse
    </div>
@endsection
