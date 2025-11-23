<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'agent') {
            return response()->json(['message' => 'Accès refusé: non agent'], 403);
        }
        return $next($request);
    }
}
