<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contribuable;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginContribuable(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $contribuable = Contribuable::where('email', $credentials['email'])->first();

        if (!$contribuable || !Hash::check($credentials['password'], $contribuable->password)) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        $token = $contribuable->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $contribuable,
            'token' => $token,
            'type' => 'contribuable'
        ]);
    }

    public function loginAgent(Request $request)
    {
        $credentials = $request->validate([
            'mail' => 'required|email',
            'password' => 'required|string'
        ]);

        $agent = Agent::where('mail', $credentials['mail'])->first();

        if (!$agent || !Hash::check($credentials['password'], $agent->password)) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        $token = $agent->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $agent,
            'token' => $token,
            'type' => 'agent'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnecté avec succès']);
    }
}
