@extends('layouts.app')

@section('title', 'Ajouter '.$config['singular'])

@php($base = $resource === 'fee-categories' ? 'admin.finance-resources' : 'admin.academic')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="card">
            <div class="card-body">
                <h2 class="mb-5">Ajouter {{ strtolower($config['singular']) }}</h2>
                <form method="POST" action="{{ route($base.'.store', $resource) }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    @include('admin.resources.form', ['item' => null])
                    <div class="flex gap-2 sm:col-span-2">
                        <button class="btn-primary">Enregistrer</button>
                        <a href="{{ route($base.'.index', $resource) }}" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
