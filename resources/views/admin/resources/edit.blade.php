@extends('layouts.app')

@section('title', 'Modifier '.$config['singular'])

@php($base = $resource === 'fee-categories' ? 'admin.finance-resources' : 'admin.academic')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="card">
            <div class="card-body">
                <h2 class="mb-5">Modifier {{ strtolower($config['singular']) }}</h2>
                <form method="POST" action="{{ route($base.'.update', [$resource, $item->getKey()]) }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    @method('PUT')
                    @include('admin.resources.form')
                    <div class="flex gap-2 sm:col-span-2">
                        <button class="btn-primary">Enregistrer</button>
                        <a href="{{ route($base.'.index', $resource) }}" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
