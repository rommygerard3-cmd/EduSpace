<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EduSpace') — {{ config('app.name', 'EduSpace') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50">
@php($user = auth()->user())
<div class="min-h-screen lg:flex">
    <aside class="hidden w-64 shrink-0 flex-col border-r border-slate-200 bg-white lg:flex">
        <div class="flex h-16 items-center border-b border-slate-100 px-6">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold tracking-tight text-blue-800">EduSpace</a>
        </div>
        <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
            <div>
                <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Général</p>
                <a href="{{ route('dashboard') }}" class="nav-link mt-1 {{ request()->routeIs('dashboard') ? 'nav-link-active' : '' }}">Tableau de bord</a>
            </div>

            @if($user->isStudent())
                <div>
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Ma scolarité</p>
                    <a href="{{ route('student.schedule') }}" class="nav-link mt-1 {{ request()->routeIs('student.schedule') ? 'nav-link-active' : '' }}">Emploi du temps</a>
                    <a href="{{ route('student.subjects') }}" class="nav-link {{ request()->routeIs('student.subjects') ? 'nav-link-active' : '' }}">Matières</a>
                    <a href="{{ route('student.grades') }}" class="nav-link {{ request()->routeIs('student.grades') ? 'nav-link-active' : '' }}">Notes</a>
                    <a href="{{ route('student.report-cards.index') }}" class="nav-link {{ request()->routeIs('student.report-cards.*') ? 'nav-link-active' : '' }}">Bulletins</a>
                    <a href="{{ route('student.payments') }}" class="nav-link {{ request()->routeIs('student.payments') ? 'nav-link-active' : '' }}">Paiements</a>
                    <a href="{{ route('student.profile') }}" class="nav-link {{ request()->routeIs('student.profile') ? 'nav-link-active' : '' }}">Mon profil</a>
                </div>
            @endif

            @if($user->isTeacher())
                <div>
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Enseignement</p>
                    <a href="{{ route('teacher.classes.index') }}" class="nav-link mt-1 {{ request()->routeIs('teacher.classes.*') ? 'nav-link-active' : '' }}">Mes classes</a>
                    <a href="{{ route('teacher.schedule') }}" class="nav-link {{ request()->routeIs('teacher.schedule') ? 'nav-link-active' : '' }}">Mon horaire</a>
                    <a href="{{ route('teacher.assessments.index') }}" class="nav-link {{ request()->routeIs('teacher.assessments.*', 'teacher.grades.*') ? 'nav-link-active' : '' }}">Évaluations</a>
                    <a href="{{ route('teacher.attendance.index') }}" class="nav-link {{ request()->routeIs('teacher.attendance.*') ? 'nav-link-active' : '' }}">Présences</a>
                    <a href="{{ route('teacher.preparations.index') }}" class="nav-link {{ request()->routeIs('teacher.preparations.*') ? 'nav-link-active' : '' }}">Préparations</a>
                    <a href="{{ route('teacher.profile') }}" class="nav-link {{ request()->routeIs('teacher.profile') ? 'nav-link-active' : '' }}">Mon profil</a>
                </div>
            @endif

            @if($user->isPrefect())
                <div>
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Préfecture</p>
                    <a href="{{ route('prefect.schedules') }}" class="nav-link mt-1 {{ request()->routeIs('prefect.schedules') ? 'nav-link-active' : '' }}">Horaires</a>
                    <a href="{{ route('prefect.statistics') }}" class="nav-link {{ request()->routeIs('prefect.statistics') ? 'nav-link-active' : '' }}">Statistiques</a>
                    <a href="{{ route('prefect.reports') }}" class="nav-link {{ request()->routeIs('prefect.reports') ? 'nav-link-active' : '' }}">Rapports</a>
                    <a href="{{ route('prefect.preparations.index') }}" class="nav-link {{ request()->routeIs('prefect.preparations.*') ? 'nav-link-active' : '' }}">Préparations</a>
                </div>
            @endif

            @if($user->isDisciplineDirector())
                <div>
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Discipline</p>
                    <a href="{{ route('discipline.students') }}" class="nav-link mt-1 {{ request()->routeIs('discipline.students') ? 'nav-link-active' : '' }}">Élèves</a>
                    <a href="{{ route('discipline.attendance') }}" class="nav-link {{ request()->routeIs('discipline.attendance') ? 'nav-link-active' : '' }}">Absences</a>
                    <a href="{{ route('discipline.incidents.index') }}" class="nav-link {{ request()->routeIs('discipline.incidents.*') ? 'nav-link-active' : '' }}">Incidents</a>
                    <a href="{{ route('discipline.convocations.index') }}" class="nav-link {{ request()->routeIs('discipline.convocations.*') ? 'nav-link-active' : '' }}">Convocations</a>
                    <a href="{{ route('discipline.conduct.index') }}" class="nav-link {{ request()->routeIs('discipline.conduct.*') ? 'nav-link-active' : '' }}">Conduite</a>
                    <a href="{{ route('discipline.notifications') }}" class="nav-link {{ request()->routeIs('discipline.notifications') ? 'nav-link-active' : '' }}">Notifications</a>
                </div>
            @endif

            @if($user->hasPermission('finances.view'))
                <div>
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Finances</p>
                    <a href="{{ route('finance.invoices.index') }}" class="nav-link mt-1 {{ request()->routeIs('finance.invoices.*') ? 'nav-link-active' : '' }}">Factures</a>
                    @if($user->hasPermission('finances.manage'))
                        <a href="{{ route('admin.fees.index') }}" class="nav-link {{ request()->routeIs('admin.fees.*') ? 'nav-link-active' : '' }}">Frais</a>
                        <a href="{{ route('admin.finance-resources.index', 'fee-categories') }}" class="nav-link {{ request()->routeIs('admin.finance-resources.*') ? 'nav-link-active' : '' }}">Catégories</a>
                    @endif
                </div>
            @endif

            @if($user->hasPermission('students.manage') || $user->hasPermission('teachers.manage') || $user->hasPermission('settings.manage'))
                <div>
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Administration</p>
                    @if($user->hasPermission('students.manage'))
                        <a href="{{ route('admin.students.index') }}" class="nav-link mt-1 {{ request()->routeIs('admin.students.*') ? 'nav-link-active' : '' }}">Élèves</a>
                    @endif
                    @if($user->hasPermission('teachers.manage'))
                        <a href="{{ route('admin.teachers.index') }}" class="nav-link {{ request()->routeIs('admin.teachers.*') ? 'nav-link-active' : '' }}">Enseignants</a>
                    @endif
                    @if($user->hasPermission('settings.manage'))
                        <a href="{{ route('admin.classes.index') }}" class="nav-link {{ request()->routeIs('admin.classes.*') ? 'nav-link-active' : '' }}">Classes</a>
                        <a href="{{ route('admin.schedules.index') }}" class="nav-link {{ request()->routeIs('admin.schedules.*') ? 'nav-link-active' : '' }}">Horaires</a>
                        <a href="{{ route('admin.academic-years.index') }}" class="nav-link {{ request()->routeIs('admin.academic-years.*') ? 'nav-link-active' : '' }}">Années scolaires</a>
                        <a href="{{ route('admin.terms.index') }}" class="nav-link {{ request()->routeIs('admin.terms.*') ? 'nav-link-active' : '' }}">Trimestres</a>
                        <a href="{{ route('admin.academic.index', 'levels') }}" class="nav-link {{ request()->routeIs('admin.academic.*') ? 'nav-link-active' : '' }}">Référentiels</a>
                    @endif
                    @if($user->hasPermission('users.manage'))
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'nav-link-active' : '' }}">Utilisateurs</a>
                    @endif
                    @if($user->hasPermission('reports.view'))
                        <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'nav-link-active' : '' }}">Rapports</a>
                    @endif
                    @if($user->hasPermission('audits.view'))
                        <a href="{{ route('admin.audits.index') }}" class="nav-link {{ request()->routeIs('admin.audits.*') ? 'nav-link-active' : '' }}">Journal d’audit</a>
                    @endif
                    @if($user->hasPermission('settings.manage'))
                        <a href="{{ route('admin.settings.edit') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'nav-link-active' : '' }}">Paramètres</a>
                    @endif
                </div>
            @endif
        </nav>
        <div class="border-t border-slate-100 p-3">
            <div class="mb-2 rounded-lg bg-slate-50 px-3 py-2">
                <p class="truncate text-sm font-semibold text-slate-800">{{ $user->name }}</p>
                <p class="truncate text-xs text-slate-500">{{ $user->matricule }} · {{ $user->roles->first()?->label }}</p>
            </div>
            <a href="{{ route('profile.edit') }}" class="nav-link">Mon profil</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="nav-link w-full text-left text-red-600 hover:bg-red-50 hover:text-red-700">Déconnexion</button>
            </form>
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur">
            <div class="flex h-16 items-center gap-3 px-4 sm:px-6">
                <button type="button" data-mobile-nav-toggle class="btn-secondary btn-sm lg:hidden" aria-label="Ouvrir le menu">Menu</button>
                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-lg font-semibold text-slate-900">@yield('title', 'Tableau de bord')</h1>
                </div>
                <form method="GET" action="{{ route('search') }}" class="hidden md:block">
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher…" class="input w-56" aria-label="Rechercher">
                </form>
                <a href="{{ route('notifications.index') }}" class="relative rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-blue-700" aria-label="Notifications">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.85 23.85 0 0 0 5.454-1.31A8.97 8.97 0 0 1 18 9.75V9a6 6 0 0 0-12 0v.75a8.97 8.97 0 0 1-2.31 6.022c1.733.64 3.56 1.085 5.454 1.31m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                    @if($user->unreadNotificationsCount() > 0)
                        <span class="absolute -right-0.5 -top-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white">{{ $user->unreadNotificationsCount() }}</span>
                    @endif
                </a>
                <a href="{{ route('profile.edit') }}" class="hidden items-center gap-2 rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 sm:flex">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-800">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    <span class="max-w-32 truncate">{{ $user->name }}</span>
                </a>
            </div>
            <nav data-mobile-nav class="hidden border-t border-slate-100 bg-white px-3 py-3 lg:hidden">
                <a href="{{ route('dashboard') }}" class="nav-link">Tableau de bord</a>
                @if($user->isStudent())
                    <a href="{{ route('student.schedule') }}" class="nav-link">Emploi du temps</a>
                    <a href="{{ route('student.grades') }}" class="nav-link">Notes</a>
                    <a href="{{ route('student.report-cards.index') }}" class="nav-link">Bulletins</a>
                    <a href="{{ route('student.payments') }}" class="nav-link">Paiements</a>
                @endif
                @if($user->isTeacher())
                    <a href="{{ route('teacher.classes.index') }}" class="nav-link">Mes classes</a>
                    <a href="{{ route('teacher.assessments.index') }}" class="nav-link">Évaluations</a>
                    <a href="{{ route('teacher.attendance.index') }}" class="nav-link">Présences</a>
                    <a href="{{ route('teacher.preparations.index') }}" class="nav-link">Préparations</a>
                @endif
                @if($user->isPrefect())
                    <a href="{{ route('prefect.preparations.index') }}" class="nav-link">Préparations</a>
                    <a href="{{ route('prefect.schedules') }}" class="nav-link">Horaires</a>
                @endif
                @if($user->isDisciplineDirector())
                    <a href="{{ route('discipline.incidents.index') }}" class="nav-link">Incidents</a>
                    <a href="{{ route('discipline.convocations.index') }}" class="nav-link">Convocations</a>
                @endif
                @if($user->hasPermission('finances.view'))
                    <a href="{{ route('finance.invoices.index') }}" class="nav-link">Factures</a>
                @endif
                @if($user->hasPermission('students.manage'))
                    <a href="{{ route('admin.students.index') }}" class="nav-link">Élèves</a>
                @endif
                @if($user->hasPermission('users.manage'))
                    <a href="{{ route('admin.users.index') }}" class="nav-link">Utilisateurs</a>
                @endif
                <a href="{{ route('profile.edit') }}" class="nav-link">Mon profil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="nav-link w-full text-left text-red-600">Déconnexion</button>
                </form>
            </nav>
        </header>

        <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert-success mb-5">{{ session('success') }}</div>
            @endif
            @if(session('warning'))
                <div class="alert-warning mb-5">{{ session('warning') }}</div>
            @endif
            @if($errors->any())
                <div class="alert-error mb-5">
                    <p class="font-semibold">Veuillez corriger les erreurs suivantes :</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('activation_code'))
                <div class="alert-warning mb-5">
                    Code d’activation : <span class="font-mono font-bold">{{ session('activation_code') }}</span>
                    — transmettez-le à l’utilisateur, il n’est plus affiché ensuite.
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
