<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SessionTimeoutController extends Controller
{
    public function save(Request $request)
    {
        $request->validate([
            'session_timeout' => ['required', 'integer', 'min:0', 'max:480'],
        ]);

        SystemSetting::set('session_timeout', (int) $request->session_timeout);

        return redirect()->to(route('admin.settings') . '?tab=sicherheit')
            ->with('success', 'Session-Timeout gespeichert.');
    }
}
