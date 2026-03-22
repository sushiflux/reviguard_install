<?php

use Illuminate\Database\Schema\Blueprint;
use Laragear\WebAuthn\Models\WebAuthnCredential;

return WebAuthnCredential::migration()->with(function (Blueprint $table) {
    // alias is already included in the base migration
});
