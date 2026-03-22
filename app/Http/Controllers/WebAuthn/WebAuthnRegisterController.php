<?php
namespace App\Http\Controllers\WebAuthn;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Laragear\WebAuthn\Http\Requests\AttestationRequest;
use Laragear\WebAuthn\Http\Requests\AttestedRequest;

use function response;

class WebAuthnRegisterController
{
    public function options(AttestationRequest $request): Responsable
    {
        return $request->fastRegistration()->toCreate();
    }

    public function register(AttestedRequest $request): JsonResponse
    {
        $credential = $request->save();

        $alias = trim($request->input('alias', ''));
        if ($alias) {
            $credential->update(['alias' => $alias]);
        }

        return response()->json(['id' => $credential->id, 'alias' => $credential->alias]);
    }
}
