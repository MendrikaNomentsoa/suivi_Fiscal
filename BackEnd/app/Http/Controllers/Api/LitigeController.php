<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Litige;
use App\Models\Contribuable;
use Illuminate\Http\Request;

class LitigeController extends Controller
{
    /**
     * Lister tous les litiges
     * GET /api/litiges
     */
    public function index()
    {
        // Récupère tous les litiges avec leur contribuable associé
        $litiges = Litige::with('contribuable')->get();
        return response()->json($litiges);
    }

    /**
     * Créer un nouveau litige
     * POST /api/litiges
     */
    public function store(Request $request)
    {
        // Validation des champs
        $data = $request->validate([
            'sujet' => 'required|string|max:255',
            'description' => 'nullable|string',
            'statut' => 'required|string|max:50',
            'id_Contribuable' => 'required|exists:contribuables,id_Contribuable',
        ]);

        // Ajouter la date automatiquement
        $data['date_ouverture'] = now();

        $litige = Litige::create($data);

        return response()->json($litige, 201);
    }

    /**
     * Afficher un litige spécifique
     * GET /api/litiges/{id}
     */
    public function show($id_Litige)
    {
        $litige = Litige::with('contribuable')->find($id_Litige);

        if (!$litige) {
            return response()->json(['message' => 'Litige non trouvé'], 404);
        }

        return response()->json($litige);
    }

    /**
     * Modifier un litige
     * PUT /api/litiges/{id}
     */
    public function update(Request $request, $id_Litige)
    {
        $litige = Litige::find($id_Litige);

        if (!$litige) {
            return response()->json(['message' => 'Litige non trouvé'], 404);
        }

        $data = $request->validate([
            'sujet' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'date_ouverture' => 'sometimes|date',
            'statut' => 'sometimes|string|max:50',
            'id_Contribuable' => 'sometimes|exists:contribuables,id_Contribuable',
        ]);

        $litige->update($data);

        return response()->json($litige);
    }

    /**
     * Supprimer un litige
     * DELETE /api/litiges/{id}
     */
    public function destroy($id_Litige)
    {
        $litige = Litige::find($id_Litige);

        if (!$litige) {
            return response()->json(['message' => 'Litige non trouvé'], 404);
        }

        $litige->delete();

        return response()->json(['message' => 'Litige supprimé avec succès']);
    }
}
