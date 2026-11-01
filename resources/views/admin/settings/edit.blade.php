@extends('layouts.app')

@section('title', 'Parametres')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        @if($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" class="card">
            @csrf
            @method('PUT')

            <div class="card-body space-y-6">
                <div>
                    <h2 class="mb-4 text-lg font-semibold">Etablissement</h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Nom de l'etablissement</label>
                            <input type="text" name="name" value="{{ $school->name }}" class="input" required>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Nom du directeur</label>
                            <input type="text" name="principal_name" value="{{ $school->principal_name }}" class="input">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Adresse</label>
                            <input type="text" name="address" value="{{ $school->address }}" class="input">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Telephone</label>
                            <input type="text" name="phone" value="{{ $school->phone }}" class="input">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                            <input type="email" name="email" value="{{ $school->email }}" class="input">
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="mb-4 text-lg font-semibold">Parametres pedagogiques et financiers</h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Note de reussite</label>
                            <input type="number" step="0.01" min="0" max="20" name="pass_mark" value="{{ $settings['pass_mark'] ?? 10 }}" class="input" required>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Devise</label>
                            <input type="text" name="currency" value="{{ $settings['currency'] ?? 'USD' }}" class="input" required>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Seuil d'alerte d'absence (%)</label>
                            <input type="number" min="1" max="100" name="attendance_alert_threshold" value="{{ $settings['attendance_alert_threshold'] ?? 20 }}" class="input" required>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Email notifications</label>
                            <input type="email" name="notification_email" value="{{ $settings['notification_email'] ?? '' }}" class="input">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">SMS notifications</label>
                            <input type="text" name="notification_sms" value="{{ $settings['notification_sms'] ?? '' }}" class="input">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="submit" class="btn-primary">Enregistrer</button>
                </div>
            </div>
        </form>
    </div>
@endsection
