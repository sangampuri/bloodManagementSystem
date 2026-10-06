<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isAdmin()) {
            return redirect('/admin')->with('error', 'That page is for regular user accounts, not admin.');
        }

        return $next($request);
    }
}