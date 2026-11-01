@extends('layouts.app')

@section('title', 'Enseignement - Preparatoires')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold">Fiches de preparation</h2>
                <a href="{{ route('teacher.preparations.create') }}" class="btn-primary">Nouvelle fiche</a>
            </div>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr><th>Date</th><th>Matiere</th><th>Classe</th><th>Statut</th><th></th></tr>
                    </thead>
                    <tbody>
                    @forelse($preparations as $preparation)
                        <tr>
                            <td class="text-xs">{{ $preparation->date?->format('d/m/Y') }}</td>
                            <td>{{ optional($preparation->subject)->name }}</td>
                            <td>{{ optional($preparation->schoolClass)->displayName() }}</td>
                            <td><span class="badge badge-amber">{{ $preparation->status }}</span></td>
                            <td class="text-right">
                                <a href="{{ route('teacher.preparations.edit', $preparation) }}" class="text-sm font-semibold text-blue-700">Modifier</a>
                                @if(in_array($preparation->status, ['draft', 'revision']))
                                    <form method="POST" action="{{ route('teacher.preparations.submit', $preparation) }}" class="inline">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="text-sm font-semibold text-emerald-700">Soumettre</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-slate-500">Aucune fiche de preparation.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
