@extends('layouts.app')

@section('title', 'Dossier élève')

@section('content')
    @php($enrollment = $student->enrollments->sortByDesc('academic_year_id')->first())
    <div class="space-y-6">
        <div class="card">
            <div class="card-body flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-start gap-4">
                    <span class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-100 text-xl font-bold text-blue-800">{{ strtoupper(substr($student->first_name, 0, 1).substr($student->last_name, 0, 1)) }}</span>
                    <div>
                        <h2 class="text-xl">{{ $student->fullName() }}</h2>
                        <p class="text-sm text-slate-500">{{ $student->matricule }} · {{ $enrollment?->schoolClass?->displayName() ?? 'Non inscrit' }}</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            {!! $student->status === 'active' ? '<span class="badge-green">Actif</span>' : '<span class="badge-slate">Archivé</span>' !!}
                            {!! $student->user ? '<span class="badge-blue">Compte créé</span>' : '<span class="badge-amber">Sans compte</span>' !!}
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.students.edit', $student) }}" class="btn-secondary btn-sm">Modifier</a>
                    @if(! $student->user)
                        <form method="POST" action="{{ route('admin.students.account', $student) }}">@csrf<button class="btn-primary btn-sm">Créer un compte</button></form>
                    @endif
                    @if($student->status === 'active')
                        <form method="POST" action="{{ route('admin.students.archive', $student) }}" data-confirm="Archiver cet élève ?">@csrf<button class="btn-secondary btn-sm">Archiver</button></form>
                    @else
                        <form method="POST" action="{{ route('admin.students.restore', $student) }}">@csrf<button class="btn-secondary btn-sm">Réactiver</button></form>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="card">
                <div class="card-body">
                    <h2 class="mb-4">Informations</h2>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">Sexe</dt><dd>{{ $student->gender === 'F' ? 'Féminin' : ($student->gender === 'M' ? 'Masculin' : '—') }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">Naissance</dt><dd class="text-right">{{ $student->birth_date?->format('d/m/Y') ?? '—' }}{{ $student->birth_place ? ' à '.$student->birth_place : '' }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">Téléphone</dt><dd>{{ $student->phone ?: '—' }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">Adresse</dt><dd class="text-right">{{ $student->address ?: '—' }}</dd></div>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="mb-4">Responsables</h2>
                    <div class="space-y-3 text-sm">
                        @forelse($student->guardians as $guardian)
                            <div class="rounded-lg bg-slate-50 p-3">
                                <p class="font-medium">{{ $guardian->first_name }} {{ $guardian->last_name }}</p>
                                <p class="text-xs text-slate-500">{{ $guardian->relationship ?: 'Responsable' }} · {{ $guardian->phone ?: 'Sans téléphone' }}</p>
                            </div>
                        @empty
                            <p class="text-slate-500">Aucun responsable enregistré.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="mb-4">Scolarité</h2>
                    <div class="space-y-2 text-sm">
                        @forelse($student->enrollments->sortByDesc('academic_year_id') as $item)
                            <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                                <span>{{ $item->academicYear?->name }}</span>
                                <span class="font-medium">{{ $item->schoolClass?->displayName() }}</span>
                            </div>
                        @empty
                            <p class="text-slate-500">Aucune inscription.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="card">
                <div class="card-body">
                    <h2 class="mb-4">Dernières notes publiées</h2>
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead><tr><th>Matière</th><th>Évaluation</th><th>Note</th></tr></thead>
                            <tbody>
                            @forelse($student->grades->where('status', 'published')->sortByDesc(fn ($grade) => $grade->assessment?->date)->take(8) as $grade)
                                <tr>
                                    <td>{{ $grade->assessment?->subject?->name }}</td>
                                    <td>{{ $grade->assessment?->title }}</td>
                                    <td class="font-semibold">{{ $grade->score }}/{{ $grade->assessment?->max_score }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-6 text-center text-slate-500">Aucune note publiée.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="mb-4">Factures</h2>
                    <div class="space-y-2 text-sm">
                        @forelse($student->invoices as $invoice)
                            <div class="flex items-center justify-between gap-3 rounded-lg bg-slate-50 px-3 py-2">
                                <div><p class="font-medium">{{ $invoice->number }}</p><p class="text-xs text-slate-500">{{ $invoice->issued_at?->format('d/m/Y') }}</p></div>
                                <div class="text-right"><p class="font-semibold">{{ number_format($invoice->total, 2) }}</p><p class="text-xs text-slate-500">Solde {{ number_format($invoice->balance(), 2) }}</p></div>
                            </div>
                        @empty
                            <p class="text-slate-500">Aucune facture.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="card">
                <div class="card-body">
                    <h2 class="mb-4">Discipline</h2>
                    <div class="space-y-2 text-sm">
                        @forelse($student->incidents as $incident)
                            <div class="flex justify-between gap-3 border-b border-slate-100 pb-2 last:border-0"><span>{{ $incident->type }} · {{ $incident->occurred_at?->format('d/m/Y') }}</span><span class="badge-slate">{{ $incident->status }}</span></div>
                        @empty
                            <p class="text-slate-500">Aucun incident.</p>
                        @endforelse
                        @foreach($student->convocations as $convocation)
                            <div class="flex justify-between gap-3 border-b border-slate-100 pb-2 last:border-0"><span>Convocation · {{ $convocation->meeting_at?->format('d/m/Y H:i') }}</span><span class="badge-slate">{{ $convocation->status }}</span></div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h2 class="mb-4">Assiduité récente</h2>
                    <div class="space-y-2 text-sm">
                        @forelse($student->attendanceRecords->whereIn('status', ['absent', 'late', 'excused'])->sortByDesc('date')->take(8) as $record)
                            <div class="flex justify-between gap-3 border-b border-slate-100 pb-2 last:border-0"><span>{{ $record->date?->format('d/m/Y') }} · {{ $record->session === 'morning' ? 'Matin' : 'Après-midi' }}</span><span class="badge-amber">{{ $record->status }}</span></div>
                        @empty
                            <p class="text-slate-500">Aucune absence ou retard.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
