@extends('layouts.app')

@section('title', 'Matières')

@section('content')
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table">
                <thead><tr><th>Matière</th><th>Enseignant</th><th>Coefficient</th></tr></thead>
                <tbody>
                @forelse($classSubjects as $assignment)
                    <tr>
                        <td class="font-medium">{{ $assignment->subject?->name }}</td>
                        <td>{{ $assignment->teacher?->fullName() ?: 'Non affecté' }}</td>
                        <td>{{ $assignment->coefficient }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="py-8 text-center text-slate-500">Aucune matière n’est associée à votre classe.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
