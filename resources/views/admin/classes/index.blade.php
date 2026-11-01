@extends('layouts.app')

@section('title', 'Classes')

@section('content')
    <div class="space-y-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" class="flex gap-2">
                <select class="input" name="year" onchange="this.form.submit()">
                    @foreach($years as $item)
                        <option value="{{ $item->id }}" @selected($year?->id === $item->id)>{{ $item->name }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('admin.classes.create') }}" class="btn-primary">Nouvelle classe</a>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Classe</th><th>Niveau</th><th>Section</th><th>Effectif</th><th>Matières</th><th>Local</th><th></th></tr></thead>
                    <tbody>
                    @forelse($classes as $class)
                        <tr>
                            <td class="font-medium text-slate-900">{{ $class->name }}</td>
                            <td>{{ $class->level?->name ?? '—' }}</td>
                            <td>{{ $class->section?->name ?? '—' }}</td>
                            <td>{{ $class->enrollments_count }}/{{ $class->capacity }}</td>
                            <td>{{ $class->class_subjects_count }}</td>
                            <td>{{ $class->room ?: '—' }}</td>
                            <td class="text-right"><a href="{{ route('admin.classes.show', $class) }}" class="text-sm font-semibold text-blue-700">Gérer</a><a href="{{ route('admin.classes.edit', $class) }}" class="ml-3 text-sm font-semibold text-slate-600">Modifier</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-slate-500">Aucune classe pour cette année.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
