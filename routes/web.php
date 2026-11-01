<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassSubjectController;
use App\Http\Controllers\ConductNoteController;
use App\Http\Controllers\ConvocationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisciplineController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PrefectController;
use App\Http\Controllers\PreparationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherPortalController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.store');
    Route::get('/activate', [AuthController::class, 'showActivation'])->name('activate.show');
    Route::post('/activate', [AuthController::class, 'activate'])->middleware('throttle:10,1')->name('activate.store');
});

Route::middleware(['auth', 'active', 'password.changed'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    Route::get('/search', [SearchController::class, 'index'])->name('search');

    Route::prefix('student')->name('student.')->middleware('role:eleve')->group(function () {
        Route::get('/', [StudentPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [StudentPortalController::class, 'profile'])->name('profile');
        Route::get('/schedule', [StudentPortalController::class, 'schedule'])->name('schedule');
        Route::get('/subjects', [StudentPortalController::class, 'subjects'])->name('subjects');
        Route::get('/grades', [StudentPortalController::class, 'grades'])->name('grades');
        Route::get('/report-cards', [StudentPortalController::class, 'reportCards'])->name('report-cards.index');
        Route::get('/report-cards/{term}', [StudentPortalController::class, 'reportCard'])->name('report-cards.show');
        Route::get('/report-cards/{term}/pdf', [StudentPortalController::class, 'reportCardPdf'])->name('report-cards.pdf');
        Route::get('/payments', [StudentPortalController::class, 'payments'])->name('payments');
    });

    Route::prefix('teacher')->name('teacher.')->middleware('role:enseignant')->group(function () {
        Route::get('/', [TeacherPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/classes', [TeacherPortalController::class, 'classes'])->name('classes.index');
        Route::get('/classes/{schoolClass}', [TeacherPortalController::class, 'classStudents'])->name('classes.show');
        Route::get('/schedule', [TeacherPortalController::class, 'schedule'])->name('schedule');
        Route::get('/profile', [TeacherPortalController::class, 'profile'])->name('profile');

        Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
        Route::get('/assessments/create', [AssessmentController::class, 'create'])->name('assessments.create');
        Route::post('/assessments', [AssessmentController::class, 'store'])->name('assessments.store');
        Route::get('/assessments/{assessment}/edit', [AssessmentController::class, 'edit'])->name('assessments.edit');
        Route::put('/assessments/{assessment}', [AssessmentController::class, 'update'])->name('assessments.update');
        Route::post('/assessments/{assessment}/publish', [AssessmentController::class, 'publish'])->name('assessments.publish');
        Route::get('/assessments/{assessment}/grades', [GradeController::class, 'edit'])->name('grades.edit');
        Route::put('/assessments/{assessment}/grades', [GradeController::class, 'update'])->name('grades.update');

        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');

        Route::get('/preparations', [PreparationController::class, 'index'])->name('preparations.index');
        Route::get('/preparations/create', [PreparationController::class, 'create'])->name('preparations.create');
        Route::post('/preparations', [PreparationController::class, 'store'])->name('preparations.store');
        Route::get('/preparations/{preparation}/edit', [PreparationController::class, 'edit'])->name('preparations.edit');
        Route::put('/preparations/{preparation}', [PreparationController::class, 'update'])->name('preparations.update');
        Route::post('/preparations/{preparation}/submit', [PreparationController::class, 'submit'])->name('preparations.submit');
    });

    Route::prefix('prefect')->name('prefect.')->middleware('role:prefet')->group(function () {
        Route::get('/', [PrefectController::class, 'dashboard'])->name('dashboard');
        Route::get('/schedules', [PrefectController::class, 'schedules'])->name('schedules');
        Route::get('/statistics', [PrefectController::class, 'statistics'])->name('statistics');
        Route::get('/reports', [PrefectController::class, 'reports'])->name('reports');
        Route::get('/preparations', [PreparationController::class, 'reviewIndex'])->name('preparations.index');
        Route::get('/preparations/{preparation}', [PreparationController::class, 'reviewShow'])->name('preparations.show');
        Route::post('/preparations/{preparation}/review', [PreparationController::class, 'review'])->name('preparations.review');
    });

    Route::prefix('discipline')->name('discipline.')->middleware('role:directeur_discipline')->group(function () {
        Route::get('/', [DisciplineController::class, 'dashboard'])->name('dashboard');
        Route::get('/students', [DisciplineController::class, 'students'])->name('students');
        Route::get('/attendance', [DisciplineController::class, 'attendance'])->name('attendance');
        Route::get('/notifications', [DisciplineController::class, 'notifications'])->name('notifications');

        Route::resource('incidents', IncidentController::class)->except('show');
        Route::resource('convocations', ConvocationController::class)->except('show');
        Route::get('/conduct', [ConductNoteController::class, 'index'])->name('conduct.index');
        Route::post('/conduct', [ConductNoteController::class, 'store'])->name('conduct.store');
    });

    Route::prefix('finance')->name('finance.')->middleware('permission:finances.view')->group(function () {
        Route::get('/', [FinanceController::class, 'dashboard'])->name('dashboard');
        Route::get('/invoices', [FinanceController::class, 'invoices'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [FinanceController::class, 'showInvoice'])->name('invoices.show');
        Route::post('/invoices/{invoice}/payments', [FinanceController::class, 'storePayment'])->name('payments.store');
        Route::post('/payments/{payment}/cancel', [FinanceController::class, 'cancelPayment'])->name('payments.cancel');
        Route::get('/payments/{payment}/receipt', [FinanceController::class, 'receipt'])->name('payments.receipt');
        Route::post('/students/{student}/invoice', [FinanceController::class, 'generateInvoice'])->name('invoices.generate');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class)->middleware('permission:users.manage');
        Route::post('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate')->middleware('permission:users.manage');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password')->middleware('permission:users.manage');
        Route::post('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle')->middleware('permission:users.manage');

        Route::resource('students', StudentController::class)->middleware('permission:students.manage');
        Route::post('/students/{student}/archive', [StudentController::class, 'archive'])->name('students.archive')->middleware('permission:students.manage');
        Route::post('/students/{student}/restore', [StudentController::class, 'restore'])->name('students.restore')->middleware('permission:students.manage');
        Route::post('/students/{student}/account', [StudentController::class, 'createAccount'])->name('students.account')->middleware('permission:students.manage');

        Route::resource('teachers', TeacherController::class)->middleware('permission:teachers.manage');
        Route::post('/teachers/{teacher}/account', [TeacherController::class, 'createAccount'])->name('teachers.account')->middleware('permission:teachers.manage');

        Route::resource('academic-years', AcademicYearController::class)->only(['index', 'store', 'destroy'])->middleware('permission:settings.manage');
        Route::post('/academic-years/{academicYear}/current', [AcademicYearController::class, 'setCurrent'])->name('academic-years.current')->middleware('permission:settings.manage');

        Route::resource('terms', TermController::class)->only(['index', 'store', 'update', 'destroy'])->middleware('permission:settings.manage');

        Route::resource('classes', SchoolClassController::class)->middleware('permission:settings.manage');
        Route::post('/classes/{class}/subjects', [ClassSubjectController::class, 'store'])->name('classes.subjects.store')->middleware('permission:settings.manage');
        Route::delete('/class-subjects/{classSubject}', [ClassSubjectController::class, 'destroy'])->name('class-subjects.destroy')->middleware('permission:settings.manage');

        Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index')->middleware('permission:settings.manage');
        Route::get('/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create')->middleware('permission:settings.manage');
        Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store')->middleware('permission:settings.manage');
        Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy')->middleware('permission:settings.manage');

        Route::get('/fees', [FeeController::class, 'index'])->name('fees.index')->middleware('permission:finances.manage');
        Route::get('/fees/create', [FeeController::class, 'create'])->name('fees.create')->middleware('permission:finances.manage');
        Route::post('/fees', [FeeController::class, 'store'])->name('fees.store')->middleware('permission:finances.manage');
        Route::post('/fees/bulk', [FeeController::class, 'bulkStore'])->name('fees.bulk')->middleware('permission:finances.manage');
        Route::delete('/fees/{fee}', [FeeController::class, 'destroy'])->name('fees.destroy')->middleware('permission:finances.manage');

        Route::middleware('permission:settings.manage')->group(function () {
            Route::get('/academic/{resource}', [ResourceController::class, 'index'])->name('academic.index');
            Route::get('/academic/{resource}/create', [ResourceController::class, 'create'])->name('academic.create');
            Route::post('/academic/{resource}', [ResourceController::class, 'store'])->name('academic.store');
            Route::get('/academic/{resource}/{id}/edit', [ResourceController::class, 'edit'])->name('academic.edit');
            Route::put('/academic/{resource}/{id}', [ResourceController::class, 'update'])->name('academic.update');
            Route::delete('/academic/{resource}/{id}', [ResourceController::class, 'destroy'])->name('academic.destroy');
        });

        Route::middleware('permission:finances.manage')->group(function () {
            Route::get('/finance/{resource}', [ResourceController::class, 'index'])->name('finance-resources.index');
            Route::get('/finance/{resource}/create', [ResourceController::class, 'create'])->name('finance-resources.create');
            Route::post('/finance/{resource}', [ResourceController::class, 'store'])->name('finance-resources.store');
            Route::get('/finance/{resource}/{id}/edit', [ResourceController::class, 'edit'])->name('finance-resources.edit');
            Route::put('/finance/{resource}/{id}', [ResourceController::class, 'update'])->name('finance-resources.update');
            Route::delete('/finance/{resource}/{id}', [ResourceController::class, 'destroy'])->name('finance-resources.destroy');
        });

        Route::get('/audits', [AuditLogController::class, 'index'])->name('audits.index')->middleware('permission:audits.view');
        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit')->middleware('permission:settings.manage');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update')->middleware('permission:settings.manage');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index')->middleware('permission:reports.view');
        Route::get('/reports/csv', [ReportController::class, 'csv'])->name('reports.csv')->middleware('permission:reports.view');
    });
});

Route::fallback(function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});
