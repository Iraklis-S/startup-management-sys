<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Unauthorized');
        }

        $userRole = optional($user->roli)->role_name;

        if (! $userRole) {
            abort(403, 'User has no role assigned');
        }

        if (! empty($roles) && ! in_array($userRole, $roles, true)) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}

