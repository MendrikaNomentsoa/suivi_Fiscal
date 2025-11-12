<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AgentController extends Controller
{
    /**
     * Lister tous les agents
     * GET /api/agents
     */
    public function index()
    {
        // Récupère tous les agents
        $agents = Agent::all();

        return response()->json($agents);
    }

    /**
     * ➕ Créer un agent
     * POST /api/agents
     */
    public function store(Request $request)
    {
        // Validation des données reçues
        $data = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'mail' => 'required|email|unique:agents,mail',
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|string|max:50',
            'password' => 'required|string|min:6',
        ]);

        // Hachage du mot de passe avant enregistrement
        $data['password'] = Hash::make($data['password']);

        $agent = Agent::create($data);

        return response()->json($agent, 201);
    }

    /**
     * Afficher un agent spécifique
     * GET /api/agents/{id}
     */
    public function show($id_Agent)
    {
        $agent = Agent::find($id_Agent);

        if (!$agent) {
            return response()->json(['message' => 'Agent non trouvé'], 404);
        }

        return response()->json($agent);
    }

    /**
     * Modifier un agent
     * PUT /api/agents/{id}
     */
    public function update(Request $request, $id_Agent)
    {
        $agent = Agent::find($id_Agent);

        if (!$agent) {
            return response()->json(['message' => 'Agent non trouvé'], 404);
        }

        $data = $request->validate([
            'nom' => 'sometimes|string|max:100',
            'prenom' => 'sometimes|string|max:100',
            'mail' => 'sometimes|email|unique:agents,mail,' . $id_Agent . ',id_Agent',
            'telephone' => 'nullable|string|max:20',
            'role' => 'sometimes|string|max:50',
            'password' => 'nullable|string|min:6',
        ]);

        // Si mot de passe envoyé → le hacher
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $agent->update($data);

        return response()->json($agent);
    }

    /**
     * Supprimer un agent
     * DELETE /api/agents/{id}
     */
    public function destroy($id_Agent)
    {
        $agent = Agent::find($id_Agent);

        if (!$agent) {
            return response()->json(['message' => 'Agent non trouvé'], 404);
        }

        $agent->delete();

        return response()->json(['message' => 'Agent supprimé avec succès']);
    }
}
