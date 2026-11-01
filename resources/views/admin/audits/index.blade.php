@extends('layouts.app')

@section('title', 'Journal d audit')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="mb-4 flex items-center justify-between">
                <h2>Journal des operations</h2>
                <form method="GET" action="{{ route('admin.audits.index') }}" class="flex gap-2">
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Action, type..." class="input w-56" aria-label="Rechercher">
                    <select name="user" class="input w-56">
                        <option value="">Tous les utilisateurs</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" @selected(request('user') == $u->id)>{{ $u->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-secondary">Filtrer</button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr><th>Date</th><th>Utilisateur</th><th>Action</th><th>Type</th><th>ID</th><th>Anciennes valeurs</th><th>Nouvelles valeurs</th></tr>
                    </thead>
                    <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="text-xs">{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                            <td>{{ $log->user?->name }}</td>
                            <td><span class="badge badge-blue">{{ $log->action }}</span></td>
                            <td class="text-xs">{{ $log->model_type }}</td>
                            <td class="text-xs">{{ $log->model_id }}</td>
                            <td class="text-xs">{{ json_encode($log->old_values) }}</td>
                            <td class="text-xs">{{ json_encode($log->new_values) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-6 text-center text-slate-500">Aucune operation enregistree.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $logs->links() }}</div>
        </div>
    </div>
@endsection
