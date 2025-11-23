<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Contribuable;
use App\Models\Agent;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token manquant'], 401);
        }

        // Vérifier le rôle
        if ($role === 'contribuable') {
            $user = Contribuable::where('api_token', $token)->first();
        } else {
            $user = Agent::where('api_token', $token)->first();
        }

        if (!$user) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        // Attacher l'utilisateur à la requête
        $request->merge(['user' => $user]);

        return $next($request);
    }
}
