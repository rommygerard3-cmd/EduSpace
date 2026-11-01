<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function edit(Request $request): View
    {
        $school = $request->user()->school;
        $settings = Setting::where('school_id', $school?->id)->pluck('value', 'key');

        return view('admin.settings.edit', compact('school', 'settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $school = $request->user()->school;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'principal_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'pass_mark' => ['required', 'numeric', 'min:0', 'max:20'],
            'currency' => ['required', 'string', 'max:10'],
            'attendance_alert_threshold' => ['required', 'integer', 'min:1', 'max:100'],
            'notification_email' => ['nullable', 'email', 'max:255'],
            'notification_sms' => ['nullable', 'string', 'max:50'],
        ]);

        DB::transaction(function () use ($school, $validated, $request) {
            $school->update([
                'name' => $validated['name'],
                'principal_name' => $validated['principal_name'] ?? null,
                'address' => $validated['address'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
            ]);

            foreach (['pass_mark', 'currency', 'attendance_alert_threshold', 'notification_email', 'notification_sms'] as $key) {
                Setting::updateOrCreate(
                    ['school_id' => $school->id, 'key' => $key],
                    ['value' => (string) ($validated[$key] ?? ''), 'group' => 'general'],
                );
            }

            $this->auditLogger->log('settings.updated', $school, [], $validated);
        });

        return back()->with('success', 'Paramètres enregistrés.');
    }
}
