<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            if (! $user->is_active) {
                Auth::logout();

                return redirect()
                    ->route('login.form')
                    ->with('error', 'Votre compte a été désactivé par un administrateur.');
            }
        }

        return $next($request);
    }
}
