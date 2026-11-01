<div>
    <label class="label" for="name">Nom de la classe</label>
    <input class="input" id="name" name="name" value="{{ old('name', $schoolClass?->name) }}" placeholder="6e A" required>
</div>
<div>
    <label class="label" for="level_id">Niveau</label>
    <select class="input" id="level_id" name="level_id" required>
        <option value="">Sélectionner</option>
        @foreach($levels as $level)
            <option value="{{ $level->id }}" @selected(old('level_id', $schoolClass?->level_id) == $level->id)>{{ $level->name }}</option>
        @endforeach
    </select>
</div>
<div>
    <label class="label" for="section_id">Section</label>
    <select class="input" id="section_id" name="section_id">
        <option value="">Aucune</option>
        @foreach($sections as $section)
            <option value="{{ $section->id }}" @selected(old('section_id', $schoolClass?->section_id) == $section->id)>{{ $section->name }}</option>
        @endforeach
    </select>
</div>
<div>
    <label class="label" for="capacity">Capacité</label>
    <input class="input" type="number" id="capacity" name="capacity" value="{{ old('capacity', $schoolClass?->capacity ?? 40) }}" min="1" max="500" required>
</div>
<div>
    <label class="label" for="room">Local</label>
    <input class="input" id="room" name="room" value="{{ old('room', $schoolClass?->room) }}" placeholder="Salle 12">
</div>
