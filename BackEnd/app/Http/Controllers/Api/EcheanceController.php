<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Echeance;
use Illuminate\Http\Request;

class EcheanceController extends Controller
{
    /**
     * Afficher toutes les échéances
     * GET /api/echeances
     */
    public function index()
    {
        // Récupère toutes les échéances avec le contribuable associé
        $echeances = Echeance::with('contribuable')->get();

        return response()->json($echeances);
    }

    /**
     * Ajouter une nouvelle échéance
     * POST /api/echeances
     */
    public function store(Request $request)
    {
        // Validation des champs
        $data = $request->validate([
            'montant' => 'required|numeric|min:0',
            'date_limite' => 'required|date',
            'penalite' => 'nullable|numeric|min:0',
            'id_Contribuable' => 'required|exists:contribuables,id_Contribuable',
        ]);

        // Création de l’échéance
        $echeance = Echeance::create($data);

        return response()->json($echeance, 201);
    }

    /**
     * Afficher une échéance spécifique
     * GET /api/echeances/{id}
     */
    public function show($id_Echeance)
    {
        $echeance = Echeance::with('contribuable')->find($id_Echeance);

        if (!$echeance) {
            return response()->json(['message' => 'Échéance non trouvée'], 404);
        }

        return response()->json($echeance);
    }

    /**
     * Modifier une échéance
     * PUT /api/echeances/{id}
     */
    public function update(Request $request, $id_Echeance)
    {
        $echeance = Echeance::find($id_Echeance);

        if (!$echeance) {
            return response()->json(['message' => 'Échéance non trouvée'], 404);
        }

        $data = $request->validate([
            'montant' => 'sometimes|numeric|min:0',
            'date_limite' => 'sometimes|date',
            'penalite' => 'nullable|numeric|min:0',
            'id_Contribuable' => 'sometimes|exists:contribuables,id_Contribuable',
        ]);

        $echeance->update($data);

        return response()->json($echeance);
    }

    /**
     * Supprimer une échéance
     * DELETE /api/echeances/{id}
     */
    public function destroy($id_Echeance)
    {
        $echeance = Echeance::find($id_Echeance);

        if (!$echeance) {
            return response()->json(['message' => 'Échéance non trouvée'], 404);
        }

        $echeance->delete();

        return response()->json(['message' => 'Échéance supprimée avec succès']);
    }
}
