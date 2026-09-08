<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordHasBeenChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->is_first_login && ! $request->routeIs('password.change.*', 'logout')) {
            return redirect()->route('password.change.edit');
        }

        return $next($request);
    }
}
