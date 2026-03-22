<?php
namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorChallengeController extends Controller
{
    public function show()
    {
        $user = User::find(session('2fa_pending_user_id'));
        if (!$user) return redirect()->route('login');

        $hasTotp     = (bool) $user->totp_enabled_at;
        $hasWebAuthn = $user->webAuthnCredentials()->whereEnabled()->count() > 0;

        return view('2fa.challenge', compact('user', 'hasTotp', 'hasWebAuthn'));
    }

    public function verifyTotp(Request $request)
    {
        $user = User::find(session('2fa_pending_user_id'));
        if (!$user) return redirect()->route('login');

        $request->validate(['code' => ['required', 'string', 'size:6', 'regex:/^[0-9]+$/']]);

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user->totp_secret, $request->code, 1);

        if (!$valid) {
            return back()->withErrors(['code' => 'Der Code ist falsch oder abgelaufen.']);
        }

        $this->completeTwoFactor($user);
        return redirect()->intended(route('dashboard'));
    }

    // Called after WebAuthn or TOTP success
    public function completeTwoFactor(User $user): void
    {
        session()->forget('2fa_pending_user_id');
        Auth::loginUsingId($user->id);
    }

    // ── TOTP Setup (from profile/settings) ──────────────────────

    public function setupTotp()
    {
        $user = auth()->user();
        $google2fa = new Google2FA();

        // Generate new secret (stored in session until confirmed)
        if (!session('totp_setup_secret')) {
            session(['totp_setup_secret' => $google2fa->generateSecretKey(32)]);
        }

        $secret = session('totp_setup_secret');
        $qrUrl  = $google2fa->getQRCodeUrl(config('app.name'), $user->username, $secret);

        $renderer = new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd());
        $qrSvg    = (new Writer($renderer))->writeString($qrUrl);

        return view('2fa.setup-totp', compact('secret', 'qrSvg'));
    }

    public function confirmTotp(Request $request)
    {
        $request->validate(['code' => ['required', 'string', 'size:6', 'regex:/^[0-9]+$/']]);

        $secret    = session('totp_setup_secret');
        $google2fa = new Google2FA();

        if (!$secret || !$google2fa->verifyKey($secret, $request->code, 1)) {
            return back()->withErrors(['code' => 'Der Code ist falsch. Bitte erneut versuchen.']);
        }

        auth()->user()->update([
            'totp_secret'     => $secret,
            'totp_enabled_at' => now(),
        ]);

        session()->forget('totp_setup_secret');
        return redirect()->to(route('profile.settings') . '?tab=2fa')
            ->with('success', 'TOTP wurde erfolgreich aktiviert.');
    }

    public function disableTotp(Request $request)
    {
        $request->validate(['password' => ['required', 'current_password']]);

        auth()->user()->update([
            'totp_secret'     => null,
            'totp_enabled_at' => null,
        ]);

        return back()->with('success', 'TOTP wurde deaktiviert.');
    }

    public function setup2fa()
    {
        $user = auth()->user();
        $credentials = $user->webAuthnCredentials()->whereEnabled()->get();
        return view('profile.2fa', compact('user', 'credentials'));
    }
}
