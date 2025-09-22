<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockStoragePublic
{
    public function handle(Request $request, Closure $next)
    {
        // Bloqueia qualquer acesso a /storage/*
        if ($request->is('storage/*')) {
            abort(404);
        }
        return $next($request);
    }
}