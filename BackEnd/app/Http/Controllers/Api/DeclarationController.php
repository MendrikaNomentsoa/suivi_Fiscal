<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Declaration;
use Illuminate\Http\Request;
use App\Models\Echeance;
use Carbon\Carbon;


class DeclarationController extends Controller
{
    /**
     * Liste de toutes les déclarations
     * GET /api/declarations
     */
    public function index()
    {
        // ATTENTION : les relations s'appellent typeImpot() et contribuable()
        $declarations = Declaration::with(['typeImpot', 'contribuable'])->get();

        return response()->json($declarations);
    }

    /**
     * Ajouter une déclaration
     * POST /api/declarations
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_type_impot' => 'required|exists:type_impots,id_type_impot',
            'id_contribuable' => 'required|exists:contribuables,id_contribuable',
            'montant' => 'required|numeric|min:0',
            'date_declaration'=> 'nullable|date',
            'statut' => 'required|in:brouillon,validee'
        ]);

        // 1️⃣ Création déclaration
        $declaration = Declaration::create($validated);

        // 2️⃣ Détermination de la date limite selon le type d'impôt
        $type = $validated['id_type_impot'];
        $date = Carbon::now();

        if ($type == 1) { // IRSA
            $date_limite = $date->clone()->addMonth()->day(15);
        }
        elseif ($type == 2) { // IS
            $exercice = $date->format('Y');
            $date_limite = Carbon::create($exercice + 1, 5, 15);
        }
        else {
            $date_limite = $date->clone()->addMonth()->day(15); // valeur par défaut
        }

        // 3️⃣ Création automatique de l’échéance
        $echeance = Echeance::create([
            'id_contribuable' => $validated['id_contribuable'],
            'id_type_impot' => $validated['id_type_impot'],
            'id_declaration' => $declaration->id,
            'montant_du' => $validated['montant'],
            'penalite' => 0,
            'interet' => 0,
            'date_limite' => $date_limite
        ]);

        return response()->json([
            'message' => 'Déclaration + échéance créées',
            'declaration' => $declaration,
            'echeance' => $echeance
        ], 201);
    }

    /**
     * Afficher une déclaration
     * GET /api/declarations/{id}
     */
    public function show($id)
    {
        $declaration = Declaration::with(['typeImpot', 'contribuable'])
                                  ->find($id);

        if (!$declaration) {
            return response()->json(['message' => 'Déclaration introuvable'], 404);
        }

        return response()->json($declaration);
    }

    /**
     * Modifier une déclaration
     * PUT /api/declarations/{id}
     */
    public function update(Request $request, $id)
    {
        $declaration = Declaration::find($id);

        if (!$declaration) {
            return response()->json(['message' => 'Déclaration introuvable'], 404);
        }

        $validated = $request->validate([
            'id_type_impot' => 'sometimes|exists:type_impots,id_type_impot',
            'id_contribuable' => 'sometimes|exists:contribuables,id_contribuable',
            'montant' => 'sometimes|numeric|min:0',
            'date_declaration'=> 'nullable|date',
            'statut' => 'sometimes|in:brouillon,validee'
        ]);

        $declaration->update($validated);

        return response()->json([
            'message' => 'Déclaration mise à jour avec succès',
            'data' => $declaration
        ]);
    }

    /**
     * Supprimer une déclaration
     * DELETE /api/declarations/{id}
     */
    public function destroy($id)
    {
        $declaration = Declaration::find($id);

        if (!$declaration) {
            return response()->json(['message' => 'Déclaration introuvable'], 404);
        }

        $declaration->delete();

        return response()->json(['message' => 'Déclaration supprimée avec succès']);
    }
}
