@php($selectedRole = old('role', $user->roles->first()?->name))
<div class="sm:col-span-2">
    <label class="label" for="name">Nom complet</label>
    <input class="input" id="name" name="name" value="{{ old('name', $user->name) }}" required>
</div>
@if($isCreate)
    <div>
        <label class="label" for="matricule">Matricule</label>
        <input class="input" id="matricule" name="matricule" value="{{ old('matricule') }}" placeholder="Généré automatiquement si vide">
    </div>
@else
    <div>
        <label class="label">Matricule</label>
        <input class="input bg-slate-50" value="{{ $user->matricule }}" disabled>
    </div>
@endif
<div>
    <label class="label" for="role">Rôle</label>
    <select class="input" id="role" name="role" required>
        @foreach($roles as $role)
            <option value="{{ $role->name }}" @selected($selectedRole === $role->name)>{{ $role->label }}</option>
        @endforeach
    </select>
</div>
<div>
    <label class="label" for="email">Email</label>
    <input class="input" type="email" id="email" name="email" value="{{ old('email', $user->email) }}">
</div>
<div>
    <label class="label" for="phone">Téléphone</label>
    <input class="input" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
</div>
@if(! $isCreate)
    <label class="flex items-center gap-2 text-sm text-slate-700 sm:col-span-2">
        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-blue-700" @checked(old('is_active', $user->is_active))>
        Compte actif
    </label>
@endif
