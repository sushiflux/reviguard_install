<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class RequirePendingTwoFactor
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('2fa_pending_user_id')) {
            return redirect()->route('login');
        }
        return $next($request);
    }
}
