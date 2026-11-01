@extends('layouts.app')

@section('title', 'Facture '.$invoice->number)

@section('content')
    @php($methods = ['cash' => 'Espèces', 'bank' => 'Virement bancaire', 'mobile_money' => 'Mobile money', 'cheque' => 'Chèque', 'other' => 'Autre'])
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <div class="card">
                <div class="card-body">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="font-mono text-sm text-slate-500">{{ $invoice->number }}</p>
                            <h2 class="mt-1 text-xl">{{ $invoice->student?->fullName() }}</h2>
                            <p class="text-sm text-slate-500">{{ $invoice->student?->matricule }} · {{ $invoice->student?->currentEnrollment?->schoolClass?->displayName() }}</p>
                        </div>
                        <div class="text-left sm:text-right">
                            <p class="stat-label">Solde</p>
                            <p class="text-2xl font-bold {{ $invoice->balance() > 0 ? 'text-red-700' : 'text-emerald-700' }}">{{ number_format($invoice->balance(), 2, ',', ' ') }}</p>
                            <span class="badge-{{ $invoice->status === 'paid' ? 'green' : ($invoice->status === 'partial' ? 'amber' : 'slate') }}">{{ ucfirst($invoice->status) }}</span>
                        </div>
                    </div>

                    <div class="mt-5 overflow-x-auto">
                        <table class="table">
                            <thead><tr><th>Libellé</th><th>Quantité</th><th>Prix unitaire</th><th>Total</th></tr></thead>
                            <tbody>
                            @foreach($invoice->items as $item)
                                <tr><td>{{ $item->label }}</td><td>{{ $item->quantity }}</td><td>{{ number_format((float) $item->unit_price, 2, ',', ' ') }}</td><td class="font-semibold">{{ number_format((float) $item->total, 2, ',', ' ') }}</td></tr>
                            @endforeach
                            </tbody>
                            <tfoot><tr><td colspan="3" class="text-right font-semibold">Total facturé</td><td class="font-bold">{{ number_format((float) $invoice->total, 2, ',', ' ') }}</td></tr></tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="mb-4">Paiements</h2>
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead><tr><th>Date</th><th>Montant</th><th>Moyen</th><th>Référence</th><th>Statut</th><th></th></tr></thead>
                            <tbody>
                            @forelse($invoice->payments as $payment)
                                <tr>
                                    <td>{{ $payment->paid_at?->format('d/m/Y H:i') }}</td>
                                    <td class="font-semibold">{{ number_format((float) $payment->amount, 2, ',', ' ') }}</td>
                                    <td>{{ $methods[$payment->method] ?? $payment->method }}</td>
                                    <td class="text-xs">{{ $payment->reference ?: '—' }}</td>
                                    <td><span class="badge-{{ $payment->status === 'valid' ? 'green' : 'red' }}">{{ $payment->status === 'valid' ? 'Valide' : 'Annulé' }}</span></td>
                                    <td class="text-right">
                                        <a href="{{ route('finance.payments.receipt', $payment) }}" class="text-sm font-semibold text-blue-700">Reçu</a>
                                        @if($payment->status === 'valid')
                                            <form method="POST" action="{{ route('finance.payments.cancel', $payment) }}" class="ml-3 inline" data-confirm="Annuler ce paiement ?">@csrf<button class="text-sm font-semibold text-red-600">Annuler</button></form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-6 text-center text-slate-500">Aucun paiement.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card h-fit">
            <div class="card-body">
                <h2 class="mb-1">Enregistrer un paiement</h2>
                <p class="mb-4 text-sm text-slate-500">Solde restant : {{ number_format($invoice->balance(), 2, ',', ' ') }}</p>
                <form method="POST" action="{{ route('finance.payments.store', $invoice) }}" class="space-y-4">
                    @csrf
                    <div><label class="label" for="amount">Montant</label><input class="input" type="number" step="0.01" min="0.01" id="amount" name="amount" value="{{ old('amount', $invoice->balance()) }}" required></div>
                    <div><label class="label" for="method">Moyen de paiement</label><select class="input" id="method" name="method" required>@foreach($methods as $value => $label)<option value="{{ $value }}" @selected(old('method') === $value)>{{ $label }}</option>@endforeach</select></div>
                    <div><label class="label" for="reference">Référence</label><input class="input" id="reference" name="reference" value="{{ old('reference') }}"></div>
                    <div><label class="label" for="paid_at">Date</label><input class="input" type="datetime-local" id="paid_at" name="paid_at" value="{{ old('paid_at', now()->format('Y-m-d\TH:i')) }}" required></div>
                    <div><label class="label" for="notes">Notes</label><textarea class="input" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea></div>
                    <button class="btn-primary w-full">Enregistrer le paiement</button>
                </form>
            </div>
        </div>
    </div>
@endsection
