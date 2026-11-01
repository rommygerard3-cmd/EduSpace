<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Connexion') — {{ config('app.name', 'EduSpace') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-blue-900">
    <div class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">
            <div class="mb-6 text-center">
                <p class="text-3xl font-bold tracking-tight text-white">EduSpace</p>
                <p class="mt-1 text-sm text-blue-200">Gestion scolaire simple et complète</p>
            </div>
            <div class="card p-6 sm:p-8">
                @if(session('success'))
                    <div class="alert-success mb-5">{{ session('success') }}</div>
                @endif
                @if(session('warning'))
                    <div class="alert-warning mb-5">{{ session('warning') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert-error mb-5">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </div>
            <p class="mt-6 text-center text-xs text-blue-200">© {{ date('Y') }} EduSpace</p>
        </div>
    </div>
</body>
</html>
