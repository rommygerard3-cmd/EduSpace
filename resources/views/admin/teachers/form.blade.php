@php($isCreate = $isCreate ?? false)
<div>
    <label class="label" for="first_name">Prénom</label>
    <input class="input" id="first_name" name="first_name" value="{{ old('first_name', $teacher->first_name) }}" required>
</div>
<div>
    <label class="label" for="last_name">Nom</label>
    <input class="input" id="last_name" name="last_name" value="{{ old('last_name', $teacher->last_name) }}" required>
</div>
<div>
    <label class="label" for="gender">Sexe</label>
    <select class="input" id="gender" name="gender">
        <option value="">Non précisé</option>
        <option value="M" @selected(old('gender', $teacher->gender) === 'M')>Masculin</option>
        <option value="F" @selected(old('gender', $teacher->gender) === 'F')>Féminin</option>
    </select>
</div>
<div>
    <label class="label" for="employee_number">Numéro d’employé</label>
    <input class="input" id="employee_number" name="employee_number" value="{{ old('employee_number', $teacher->employee_number) }}">
</div>
<div>
    <label class="label" for="phone">Téléphone</label>
    <input class="input" id="phone" name="phone" value="{{ old('phone', $teacher->phone) }}">
</div>
<div>
    <label class="label" for="email">Email professionnel</label>
    <input class="input" type="email" id="email" name="email" value="{{ old('email', $teacher->email) }}">
</div>
<div>
    <label class="label" for="hire_date">Date d’embauche</label>
    <input class="input" type="date" id="hire_date" name="hire_date" value="{{ old('hire_date', $teacher->hire_date?->format('Y-m-d')) }}">
</div>
<div class="sm:col-span-2">
    <label class="label" for="address">Adresse</label>
    <input class="input" id="address" name="address" value="{{ old('address', $teacher->address) }}">
</div>
@if($isCreate)
    <div>
        <label class="label" for="matricule">Matricule du compte</label>
        <input class="input" id="matricule" name="matricule" value="{{ old('matricule') }}" placeholder="Généré automatiquement si vide">
    </div>
    <div>
        <label class="label" for="account_email">Email du compte</label>
        <input class="input" type="email" id="account_email" name="account_email" value="{{ old('account_email') }}">
    </div>
    <div class="sm:col-span-2">
        <label class="flex items-center gap-2 text-sm text-slate-700"><input type="checkbox" name="create_account" value="1" class="rounded border-slate-300 text-blue-700" @checked(old('create_account'))> Créer un compte de connexion</label>
    </div>
@else
    <div>
        <label class="label" for="account_email">Email du compte</label>
        <input class="input" type="email" id="account_email" name="account_email" value="{{ old('account_email', $teacher->user?->email) }}">
    </div>
    <div class="sm:col-span-2">
        <label class="flex items-center gap-2 text-sm text-slate-700"><input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-blue-700" @checked(old('is_active', $teacher->user?->is_active ?? true))> Compte actif</label>
    </div>
@endif
