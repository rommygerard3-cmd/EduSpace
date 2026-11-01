@extends('layouts.app')

@section('title', 'Détail utilisateur')

@section('content')
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="card xl:col-span-1">
            <div class="card-body">
                <div class="flex items-center gap-3">
                    <span class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-100 text-xl font-bold text-blue-800">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    <div>
                        <h2>{{ $user->name }}</h2>
                        <p class="text-sm text-slate-500">{{ $user->roles->map->label->join(', ') }}</p>
                    </div>
                </div>
                <dl class="mt-5 space-y-3 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-slate-500">Matricule</dt><dd class="font-mono font-medium">{{ $user->matricule }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-slate-500">Email</dt><dd class="text-right">{{ $user->email ?: '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-slate-500">Téléphone</dt><dd>{{ $user->phone ?: '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-slate-500">État</dt><dd>{!! $user->is_active ? '<span class="badge-green">Actif</span>' : '<span class="badge-red">Désactivé</span>' !!}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-slate-500">Activé le</dt><dd>{{ $user->activated_at?->format('d/m/Y H:i') ?? 'Non activé' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-slate-500">Dernière connexion</dt><dd>{{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Jamais' }}</dd></div>
                </dl>
                <div class="mt-5 flex flex-wrap gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn-secondary btn-sm">Modifier</a>
                    <form method="POST" action="{{ route('admin.users.toggle', $user) }}">@csrf<button class="btn-secondary btn-sm">{{ $user->is_active ? 'Désactiver' : 'Réactiver' }}</button></form>
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" data-confirm="Désactiver ce compte ?">@csrf @method('DELETE')<button class="btn-danger btn-sm">Désactiver</button></form>
                </div>
            </div>
        </div>

        <div class="card xl:col-span-2">
            <div class="card-body">
                <h2 class="mb-4">Activité récente</h2>
                <div class="space-y-3">
                    @forelse($user->auditLogs as $log)
                        <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-3 last:border-0 last:pb-0">
                            <div><p class="text-sm font-medium">{{ $log->action }}</p><p class="text-xs text-slate-500">{{ $log->created_at?->format('d/m/Y H:i') }}</p></div>
                            <span class="badge-slate">{{ class_basename($log->model_type) ?: '—' }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Aucune activité.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
