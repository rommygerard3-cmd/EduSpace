@extends('layouts.app')

@section('title', 'Utilisateurs')

@section('content')
    <div class="space-y-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" class="flex flex-1 flex-col gap-2 sm:flex-row">
                <input class="input sm:max-w-xs" type="search" name="q" value="{{ request('q') }}" placeholder="Nom, matricule ou email">
                <select class="input sm:max-w-xs" name="role">
                    <option value="">Tous les rôles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" @selected(request('role') === $role->name)>{{ $role->label }}</option>
                    @endforeach
                </select>
                <button class="btn-secondary">Filtrer</button>
            </form>
            <a href="{{ route('admin.users.create') }}" class="btn-primary">Nouvel utilisateur</a>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Nom</th><th>Matricule</th><th>Rôle</th><th>État</th><th>Dernière connexion</th><th></th></tr></thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <p class="font-medium text-slate-900">{{ $user->name }}</p>
                                <p class="text-xs text-slate-500">{{ $user->email ?: '—' }}</p>
                            </td>
                            <td class="font-mono text-xs">{{ $user->matricule }}</td>
                            <td>{{ $user->roles->map->label->join(', ') ?: '—' }}</td>
                            <td>
                                @if(! $user->is_active)
                                    <span class="badge-red">Désactivé</span>
                                @elseif($user->activated_at)
                                    <span class="badge-green">Actif</span>
                                @else
                                    <span class="badge-amber">Non activé</span>
                                @endif
                            </td>
                            <td class="text-xs text-slate-500">{{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Jamais' }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-sm font-semibold text-blue-700">Voir</a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="ml-3 text-sm font-semibold text-slate-600">Modifier</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-slate-500">Aucun utilisateur.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{ $users->links() }}
    </div>
@endsection
