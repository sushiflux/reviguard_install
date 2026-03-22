<?php
namespace App\Http\Controllers\WebAuthn;

use App\Models\User;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use Laragear\WebAuthn\Http\Requests\AssertedRequest;
use Laragear\WebAuthn\Http\Requests\AssertionRequest;

class WebAuthnTwoFactorController
{
    public function options(AssertionRequest $request): Responsable
    {
        $user = User::find(session('2fa_pending_user_id'));
        if (!$user) abort(403);
        return $request->toVerify($user);
    }

    public function verify(AssertedRequest $request): Response
    {
        $pendingId = session('2fa_pending_user_id');
        if (!$pendingId) return response()->json(['error' => 'Session expired'], 422);

        $user = $request->login();

        if (!$user || $user->id != $pendingId) {
            return response()->json(['error' => 'Verification failed'], 422);
        }

        session()->forget('2fa_pending_user_id');
        return response()->noContent();
    }
}
