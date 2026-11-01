@extends('layouts.app')

@section('title', 'Recherche')

@section('content')
    <div class="card">
        <div class="card-body">
            <h2 class="mb-4 text-lg font-semibold">Resultats de la recherche</h2>
            <form method="GET" action="{{ route('search') }}" class="mb-6 flex gap-2">
                <input type="search" name="q" value="{{ $query }}" placeholder="Nom, matricule, classe, facture..." class="input" aria-label="Rechercher">
                <button type="submit" class="btn-primary">Rechercher</button>
            </form>

            @if($query === '')
                <p class="text-slate-500">Saisissez un terme de recherche.</p>
            @else
                <div class="grid gap-6 lg:grid-cols-2">
                    <div>
                        <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-slate-400">Eleves</h3>
                        <div class="space-y-2">
                            @forelse($students as $student)
                                <a href="{{ route('admin.students.show', $student) }}" class="block rounded-lg bg-slate-50 px-3 py-2 hover:bg-slate-100">
                                    <span class="font-medium">{{ $student->fullName() }}</span>
                                    <span class="ml-2 font-mono text-xs text-slate-500">{{ $student->matricule }}</span>
                                </a>
                            @empty
                                <p class="text-sm text-slate-500">Aucun eleve trouve.</p>
                            @endforelse
                        </div>
                    </div>
                    <div>
                        <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-slate-400">Enseignants</h3>
                        <div class="space-y-2">
                            @forelse($teachers as $teacher)
                                <a href="{{ route('admin.teachers.show', $teacher) }}" class="block rounded-lg bg-slate-50 px-3 py-2 hover:bg-slate-100">
                                    <span class="font-medium">{{ $teacher->fullName() }}</span>
                                    <span class="ml-2 font-mono text-xs text-slate-500">{{ $teacher->employee_number }}</span>
                                </a>
                            @empty
                                <p class="text-sm text-slate-500">Aucun enseignant trouve.</p>
                            @endforelse
                        </div>
                    </div>
                    <div>
                        <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-slate-400">Classes</h3>
                        <div class="space-y-2">
                            @forelse($classes as $class)
                                <a href="{{ route('admin.classes.show', $class) }}" class="block rounded-lg bg-slate-50 px-3 py-2 hover:bg-slate-100">
                                    <span class="font-medium">{{ $class->displayName() }}</span>
                                </a>
                            @empty
                                <p class="text-sm text-slate-500">Aucune classe trouvee.</p>
                            @endforelse
                        </div>
                    </div>
                    <div>
                        <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-slate-400">Factures</h3>
                        <div class="space-y-2">
                            @forelse($invoices as $invoice)
                                <a href="{{ route('finance.invoices.show', $invoice) }}" class="block rounded-lg bg-slate-50 px-3 py-2 hover:bg-slate-100">
                                    <span class="font-medium">{{ $invoice->number }}</span>
                                    <span class="ml-2 font-mono text-xs text-slate-500">{{ $invoice->student?->fullName() }}</span>
                                </a>
                            @empty
                                <p class="text-sm text-slate-500">Aucune facture trouvee.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
