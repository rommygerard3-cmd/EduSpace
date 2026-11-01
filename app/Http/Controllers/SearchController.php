<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->input('q'));
        $schoolId = $request->user()->school_id;

        $students = collect();
        $teachers = collect();
        $classes = collect();
        $users = collect();
        $invoices = collect();

        if ($query !== '') {
            $term = '%'.$query.'%';

            $students = Student::where('school_id', $schoolId)
                ->where(fn ($inner) => $inner->where('first_name', 'like', $term)->orWhere('last_name', 'like', $term)->orWhere('matricule', 'like', $term))
                ->limit(20)
                ->get();

            $teachers = Teacher::where('school_id', $schoolId)
                ->where(fn ($inner) => $inner->where('first_name', 'like', $term)->orWhere('last_name', 'like', $term)->orWhere('employee_number', 'like', $term))
                ->limit(20)
                ->get();

            $classes = SchoolClass::where('school_id', $schoolId)
                ->where('name', 'like', $term)
                ->with('academicYear')
                ->limit(20)
                ->get();

            if ($request->user()->hasPermission('users.manage')) {
                $users = User::where('school_id', $schoolId)
                    ->where(fn ($inner) => $inner->where('name', 'like', $term)->orWhere('matricule', 'like', $term)->orWhere('email', 'like', $term))
                    ->limit(20)
                    ->get();
            }

            if ($request->user()->hasPermission('finances.view')) {
                $invoices = Invoice::whereHas('student', fn ($student) => $student->where('school_id', $schoolId))
                    ->where(fn ($inner) => $inner->where('number', 'like', $term)->orWhereHas('student', fn ($student) => $student->where('first_name', 'like', $term)->orWhere('last_name', 'like', $term)->orWhere('matricule', 'like', $term)))
                    ->with('student')
                    ->limit(20)
                    ->get();
            }
        }

        return view('search.index', compact('query', 'students', 'teachers', 'classes', 'users', 'invoices'));
    }
}
