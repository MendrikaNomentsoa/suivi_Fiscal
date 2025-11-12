<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Litige;
use Illuminate\Http\Request;

class TraiterController extends Controller
{
    /**
     * Lister les litiges en attente (tous les litiges non encore assignés)
     */
    public function index()
    {
        $litiges = Litige::where('statut', 'en_attente')
                         ->with('contribuable') // pour voir qui a créé le litige
                         ->get();

        return response()->json($litiges);
    }

    /**
     * Assigner un litige à un agent
     */
    public function assignerLitige(Request $request)
    {
        $data = $request->validate([
            'id_Litige' => 'required|exists:litiges,id_Litige',
            'id_Agent' => 'required|exists:agents,id_Agent',
        ]);

        $litige = Litige::with('agents')->findOrFail($data['id_Litige']);
        $agent = Agent::findOrFail($data['id_Agent']);

        // Vérifie si l'agent est déjà assigné
        if ($litige->agents->contains($agent->id_Agent)) {
            return response()->json(['message' => 'Cet agent est déjà assigné à ce litige.']);
        }

        // Assigne l’agent
        $litige->agents()->attach($agent->id_Agent);
        $litige->statut = 'en_cours';
        $litige->save();

        return response()->json([
            'message' => 'Litige assigné à l’agent avec succès',
            'litige' => $litige
        ]);
    }

    /**
     * Retirer un agent d’un litige
     */
    public function destroy(Request $request)
    {
        $data = $request->validate([
            'id_Agent' => 'required|exists:agents,id_Agent',
            'id_Litige' => 'required|exists:litiges,id_Litige',
        ]);

        $litige = Litige::findOrFail($data['id_Litige']);
        $litige->agents()->detach($data['id_Agent']);

        // Si plus aucun agent, on remet le statut à "en_attente"
        if ($litige->agents()->count() == 0) {
            $litige->statut = 'en_attente';
            $litige->save();
        }

        return response()->json([
            'message' => 'Agent retiré du litige avec succès'
        ]);
    }
}
