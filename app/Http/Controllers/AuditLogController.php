<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $schoolId = $request->user()->school_id;

        $logs = AuditLog::query()
            ->whereHas('user', fn ($query) => $query->where('school_id', $schoolId))
            ->with('user')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(fn ($inner) => $inner->where('action', 'like', $term)->orWhere('model_type', 'like', $term));
            })
            ->when($request->filled('user'), fn ($query) => $query->where('user_id', $request->integer('user')))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('to')))
            ->latest('created_at')
            ->paginate(50)
            ->withQueryString();

        return view('admin.audits.index', [
            'logs' => $logs,
            'users' => User::where('school_id', $schoolId)->orderBy('name')->get(),
            'actions' => AuditLog::whereHas('user', fn ($query) => $query->where('school_id', $schoolId))->distinct()->orderBy('action')->pluck('action'),
        ]);
    }
}
