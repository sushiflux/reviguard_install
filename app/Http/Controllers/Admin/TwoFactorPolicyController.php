<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class TwoFactorPolicyController extends Controller
{
    public function show()
    {
        $policy = SystemSetting::get('2fa_policy', 'none');
        return view('admin.2fa-policy', compact('policy'));
    }

    public function save(Request $request)
    {
        $request->validate(['policy' => ['required', 'in:none,any,totp,webauthn']]);
        SystemSetting::set('2fa_policy', $request->policy);
        return redirect()->to(route('admin.settings') . '?tab=sicherheit')
            ->with('success', 'Richtlinie wurde gespeichert.');
    }
}
