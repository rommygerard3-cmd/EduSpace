@php($isCreate = $isCreate ?? false)
<div>
    <label class="label" for="first_name">Prénom</label>
    <input class="input" id="first_name" name="first_name" value="{{ old('first_name', $student->first_name) }}" required>
</div>
<div>
    <label class="label" for="last_name">Nom</label>
    <input class="input" id="last_name" name="last_name" value="{{ old('last_name', $student->last_name) }}" required>
</div>
@if($isCreate)
    <div>
        <label class="label" for="matricule">Matricule</label>
        <input class="input" id="matricule" name="matricule" value="{{ old('matricule') }}" placeholder="Généré automatiquement si vide">
    </div>
@else
    <div>
        <label class="label">Matricule</label>
        <input class="input bg-slate-50" value="{{ $student->matricule }}" disabled>
    </div>
@endif
<div>
    <label class="label" for="gender">Sexe</label>
    <select class="input" id="gender" name="gender">
        <option value="">Non précisé</option>
        <option value="M" @selected(old('gender', $student->gender) === 'M')>Masculin</option>
        <option value="F" @selected(old('gender', $student->gender) === 'F')>Féminin</option>
    </select>
</div>
<div>
    <label class="label" for="birth_date">Date de naissance</label>
    <input class="input" type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $student->birth_date?->format('Y-m-d')) }}">
</div>
<div>
    <label class="label" for="birth_place">Lieu de naissance</label>
    <input class="input" id="birth_place" name="birth_place" value="{{ old('birth_place', $student->birth_place) }}">
</div>
<div>
    <label class="label" for="phone">Téléphone</label>
    <input class="input" id="phone" name="phone" value="{{ old('phone', $student->phone) }}">
</div>
<div class="sm:col-span-2">
    <label class="label" for="address">Adresse</label>
    <input class="input" id="address" name="address" value="{{ old('address', $student->address) }}">
</div>
<div>
    <label class="label" for="school_class_id">Classe</label>
    <select class="input" id="school_class_id" name="school_class_id" @required($isCreate)>
        <option value="">Sélectionner</option>
        @foreach($classes as $class)
            <option value="{{ $class->id }}" @selected((string) old('school_class_id', $currentEnrollment?->school_class_id) === (string) $class->id)>{{ $class->displayName() }}</option>
        @endforeach
    </select>
    @if(! $year)
        <p class="mt-1 text-xs text-red-600">Aucune année scolaire active.</p>
    @endif
</div>
@if($isCreate)
    <div>
        <label class="label" for="email">Email du compte élève</label>
        <input class="input" type="email" id="email" name="email" value="{{ old('email') }}">
    </div>
    <div class="sm:col-span-2">
        <label class="flex items-center gap-2 text-sm text-slate-700">
            <input type="checkbox" name="create_account" value="1" class="rounded border-slate-300 text-blue-700" @checked(old('create_account'))>
            Créer un compte de connexion pour cet élève
        </label>
    </div>
    <div class="sm:col-span-2 rounded-lg border border-slate-200 bg-slate-50 p-4">
        <h3 class="mb-3 text-sm font-semibold text-slate-800">Responsable légal (facultatif)</h3>
        <div class="grid gap-4 sm:grid-cols-2">
            <div><label class="label" for="guardian_first_name">Prénom</label><input class="input" id="guardian_first_name" name="guardian_first_name" value="{{ old('guardian_first_name') }}"></div>
            <div><label class="label" for="guardian_last_name">Nom</label><input class="input" id="guardian_last_name" name="guardian_last_name" value="{{ old('guardian_last_name') }}"></div>
            <div><label class="label" for="guardian_relationship">Lien avec l’élève</label><input class="input" id="guardian_relationship" name="guardian_relationship" value="{{ old('guardian_relationship') }}" placeholder="Père, mère, tuteur…"></div>
            <div><label class="label" for="guardian_phone">Téléphone</label><input class="input" id="guardian_phone" name="guardian_phone" value="{{ old('guardian_phone') }}"></div>
        </div>
    </div>
@else
    <div>
        <label class="label" for="email">Email du compte élève</label>
        <input class="input" type="email" id="email" name="email" value="{{ old('email', $student->user?->email) }}">
    </div>
    <div>
        <label class="label" for="status">État</label>
        <select class="input" id="status" name="status" required>
            <option value="active" @selected(old('status', $student->status) === 'active')>Actif</option>
            <option value="archived" @selected(old('status', $student->status) === 'archived')>Archivé</option>
        </select>
    </div>
@endif
