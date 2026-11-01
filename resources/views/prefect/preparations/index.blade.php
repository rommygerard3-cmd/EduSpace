@extends('layouts.app')

@section('title', 'Fiches de préparation')

@section('content')
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table">
                <thead><tr><th>Date</th><th>Enseignant</th><th>Classe</th><th>Sujet</th><th>Statut</th><th></th></tr></thead>
                <tbody>
                @forelse($preparations as $preparation)
                    <tr>
                        <td>{{ $preparation->date?->format('d/m/Y') }}</td>
                        <td>{{ $preparation->teacher?->fullName() }}</td>
                        <td>{{ $preparation->schoolClass?->name }} · {{ $preparation->subject?->name }}</td>
                        <td class="font-medium">{{ $preparation->topic }}</td>
                        <td><span class="badge-{{ $preparation->status === 'submitted' ? 'amber' : ($preparation->status === 'approved' ? 'green' : 'slate') }}">{{ $preparation->statusLabel() }}</span></td>
                        <td class="text-right"><a href="{{ route('prefect.preparations.show', $preparation) }}" class="text-sm font-semibold text-blue-700">Examiner</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-8 text-center text-slate-500">Aucune fiche à examiner.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $preparations->links() }}</div>
    </div>
@endsection
