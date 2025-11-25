<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TypeImpot;
use Illuminate\Http\Request;

class TypeImpotController extends Controller
{
    /**
     * Liste de tous les types d'impôts
     * GET /api/types-impots
     */
    public function index()
    {
        $typesImpots = TypeImpot::all();

        return response()->json($typesImpots);
    }

    /**
     * Afficher un type d'impôt spécifique
     * GET /api/types-impots/{id}
     */
    public function show($id)
    {
        $typeImpot = TypeImpot::find($id);

        if (!$typeImpot) {
            return response()->json([
                'success' => false,
                'message' => 'Type d\'impôt introuvable'
            ], 404);
        }

        return response()->json($typeImpot);
    }

    /**
     * Créer un nouveau type d'impôt
     * POST /api/types-impots
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'taux' => 'required|numeric|min:0|max:100',
            'periodicite' => 'required|string|in:mensuelle,annuelle,trimestrielle'
        ]);

        $typeImpot = TypeImpot::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Type d\'impôt créé avec succès',
            'type_impot' => $typeImpot
        ], 201);
    }

    /**
     * Mettre à jour un type d'impôt
     * PUT /api/types-impots/{id}
     */
    public function update(Request $request, $id)
    {
        $typeImpot = TypeImpot::find($id);

        if (!$typeImpot) {
            return response()->json([
                'success' => false,
                'message' => 'Type d\'impôt introuvable'
            ], 404);
        }

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'taux' => 'sometimes|numeric|min:0|max:100',
            'periodicite' => 'sometimes|string|in:mensuelle,annuelle,trimestrielle'
        ]);

        $typeImpot->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Type d\'impôt mis à jour avec succès',
            'type_impot' => $typeImpot
        ]);
    }

    /**
     * Supprimer un type d'impôt
     * DELETE /api/types-impots/{id}
     */
    public function destroy($id)
    {
        $typeImpot = TypeImpot::find($id);

        if (!$typeImpot) {
            return response()->json([
                'success' => false,
                'message' => 'Type d\'impôt introuvable'
            ], 404);
        }

        $typeImpot->delete();

        return response()->json([
            'success' => true,
            'message' => 'Type d\'impôt supprimé avec succès'
        ]);
    }
}
