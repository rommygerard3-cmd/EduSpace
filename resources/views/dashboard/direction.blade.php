@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm text-slate-500">Vue d’ensemble de l’établissement</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">{{ auth()->user()->school?->name }}</h2>
                <p class="text-sm text-slate-500">{{ $year ? 'Année '.$year->name : 'Aucune année active' }}</p>
            </div>
            @if($year)
                <div class="flex gap-2">
                    <a href="{{ route('admin.reports.index') }}" class="btn-secondary btn-sm">Rapports</a>
                    <a href="{{ route('admin.students.create') }}" class="btn-primary btn-sm">Inscrire un élève</a>
                </div>
            @endif
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="card"><div class="card-body"><p class="stat-label">Élèves actifs</p><p class="stat-value mt-2">{{ number_format($stats['students']) }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Enseignants</p><p class="stat-value mt-2">{{ number_format($stats['teachers']) }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Classes</p><p class="stat-value mt-2">{{ number_format($stats['classes']) }}</p></div></div>
            <div class="card"><div class="card-body"><p class="stat-label">Taux de présence</p><p class="stat-value mt-2">{{ $stats['attendance']['rate'] !== null ? $stats['attendance']['rate'].' %' : '—' }}</p></div></div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="card xl:col-span-2">
                <div class="card-body">
                    <div class="mb-4 flex items-center justify-between">
                        <h2>Classes et effectifs</h2>
                        <a href="{{ route('admin.classes.index') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-800">Voir tout</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead><tr><th>Classe</th><th>Effectif</th><th>Capacité</th><th></th></tr></thead>
                            <tbody>
                            @forelse($classes as $class)
                                <tr>
                                    <td class="font-medium text-slate-900">{{ $class->displayName() }}</td>
                                    <td>{{ $class->enrollments_count }}</td>
                                    <td>{{ $class->capacity }}</td>
                                    <td class="text-right"><a href="{{ route('admin.classes.show', $class) }}" class="text-sm font-semibold text-blue-700">Détail</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-6 text-center text-slate-500">Aucune classe.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="mb-4">Finances</h2>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between"><dt class="text-slate-500">Facturé</dt><dd class="font-semibold">{{ number_format($finance['invoiced'], 2) }}</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">Encaissé</dt><dd class="font-semibold text-emerald-700">{{ number_format($finance['paid'], 2) }}</dd></div>
                            <div class="flex justify-between border-t border-slate-100 pt-3"><dt class="text-slate-500">Solde</dt><dd class="font-bold text-red-700">{{ number_format($finance['balance'], 2) }}</dd></div>
                        </dl>
                        @if(auth()->user()->hasPermission('finances.view'))
                            <a href="{{ route('finance.invoices.index') }}" class="btn-secondary btn-sm mt-4 w-full">Voir les factures</a>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h2 class="mb-3">Alertes</h2>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center justify-between rounded-lg bg-amber-50 px-3 py-2 text-amber-800"><span>Préparations à examiner</span><span class="font-bold">{{ $pendingPreparations }}</span></div>
                            <div class="flex items-center justify-between rounded-lg bg-red-50 px-3 py-2 text-red-800"><span>Absences enregistrées</span><span class="font-bold">{{ $absences }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="mb-4">Activité récente</h2>
                <div class="space-y-3">
                    @forelse($recentAudit as $log)
                        <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-3 last:border-0 last:pb-0">
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $log->action }}</p>
                                <p class="text-xs text-slate-500">{{ $log->user?->name ?? 'Système' }} · {{ $log->created_at?->format('d/m/Y H:i') }}</p>
                            </div>
                            <span class="badge-slate">{{ class_basename($log->model_type) ?: '—' }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Aucune activité enregistrée.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
