@extends('layouts.app')

@section('title', $config['title'])

@php($base = $resource === 'fee-categories' ? 'admin.finance-resources' : 'admin.academic')

@section('content')
    <div class="space-y-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2>{{ $config['title'] }}</h2>
                <p class="text-sm text-slate-500">{{ $items->total() }} élément(s)</p>
            </div>
            <a href="{{ route($base.'.create', $resource) }}" class="btn-primary">Ajouter {{ strtolower($config['singular']) }}</a>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                    <tr>
                        @foreach($config['fields'] as $name => $field)<th>{{ $field['label'] }}</th>@endforeach
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($items as $item)
                        <tr>
                            @foreach($config['fields'] as $name => $field)
                                <td>
                                    @if($field['type'] === 'checkbox')
                                        {!! $item->{$name} ? '<span class="badge-green">Oui</span>' : '<span class="badge-slate">Non</span>' !!}
                                    @elseif($field['type'] === 'date')
                                        {{ $item->{$name}?->format('d/m/Y') ?? '—' }}
                                    @elseif($field['type'] === 'number' && in_array($name, ['amount'], true))
                                        {{ number_format((float) $item->{$name}, 2, ',', ' ') }}
                                    @else
                                        {{ $item->{$name} ?: '—' }}
                                    @endif
                                </td>
                            @endforeach
                            <td class="text-right">
                                <a href="{{ route($base.'.edit', [$resource, $item->getKey()]) }}" class="text-sm font-semibold text-blue-700">Modifier</a>
                                <form method="POST" action="{{ route($base.'.destroy', [$resource, $item->getKey()]) }}" class="ml-3 inline" data-confirm="Supprimer cet élément ?">@csrf @method('DELETE')<button class="text-sm font-semibold text-red-600">Supprimer</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($config['fields']) + 1 }}" class="py-8 text-center text-slate-500">Aucun élément.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{ $items->links() }}
    </div>
@endsection
