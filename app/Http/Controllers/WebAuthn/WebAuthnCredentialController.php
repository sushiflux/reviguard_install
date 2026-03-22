<?php
namespace App\Http\Controllers\WebAuthn;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WebAuthnCredentialController extends Controller
{
    public function update(Request $request, string $id)
    {
        $request->validate(['alias' => ['required', 'string', 'max:60']]);

        auth()->user()->webAuthnCredentials()
            ->where('id', $id)
            ->update(['alias' => $request->alias]);

        return back()->with('success', 'Name wurde gespeichert.');
    }

    public function destroy(string $id)
    {
        auth()->user()->webAuthnCredentials()->where('id', $id)->delete();
        return back()->with('success', 'YubiKey wurde entfernt.');
    }
}
