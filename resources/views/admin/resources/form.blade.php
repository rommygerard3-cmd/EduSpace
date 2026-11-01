@foreach($config['fields'] as $name => $field)
    <div class="{{ in_array($field['type'], ['textarea'], true) ? 'sm:col-span-2' : '' }}">
        @if($field['type'] === 'checkbox')
            <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                <input type="checkbox" name="{{ $name }}" value="1" class="rounded border-slate-300 text-blue-700" @checked(old($name, $item?->{$name}))>
                {{ $field['label'] }}
            </label>
        @else
            <label class="label" for="{{ $name }}">{{ $field['label'] }}</label>
            @if($field['type'] === 'textarea')
                <textarea class="input" id="{{ $name }}" name="{{ $name }}" rows="3">{{ old($name, $item?->{$name}) }}</textarea>
            @elseif($field['type'] === 'date')
                <input class="input" type="date" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $item?->{$name}?->format('Y-m-d')) }}">
            @else
                <input class="input" type="{{ $field['type'] === 'number' ? 'number' : 'text' }}" @if($field['type'] === 'number') step="0.01" @endif id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $item?->{$name}) }}" @required(! ($field['nullable'] ?? false))>
            @endif
        @endif
    </div>
@endforeach
