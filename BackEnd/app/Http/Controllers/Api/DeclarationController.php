<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Declaration;
use App\Models\Echeance;
use App\Models\TypeImpot;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DeclarationController extends Controller
{
    /**
     * Liste de toutes les déclarations
     * GET /api/declarations
     */
    public function index()
    {
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

        // Création de la déclaration
        $declaration = Declaration::create($validated);

        // Détermination de la date limite selon le type d'impôt
        $date = Carbon::now();
        $type = $validated['id_type_impot'];

        if ($type == 1) { // IRSA
            $date_limite = $date->copy()->addMonth()->day(15);
        } elseif ($type == 2) { // IS
            $date_limite = Carbon::create($date->year + 1, 5, 15);
        } else {
            $date_limite = $date->copy()->addMonth()->day(15); // défaut
        }

        // Création automatique de l’échéance
        $echeance = Echeance::create([
            'id_contribuable' => $validated['id_contribuable'],
            'id_declaration' => $declaration->id,
            'id_type_impot' => $validated['id_type_impot'],
            'montant_du' => $validated['montant'],
            'penalite' => 0,
            'interet' => 0,
            'date_limite' => $date_limite
        ]);

        return response()->json([
            'message' => 'Déclaration et échéance créées avec succès',
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
        $declaration = Declaration::with(['typeImpot', 'contribuable'])->find($id);

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

        // Interdire modification si validée
        if ($declaration->statut === 'validee') {
            return response()->json([
                'message' => 'Cette déclaration est validée et ne peut plus être modifiée.'
            ], 403);
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
            'declaration' => $declaration
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

        // Interdire suppression si validée
        if ($declaration->statut === 'validee') {
            return response()->json([
                'message' => 'Une déclaration validée ne peut pas être supprimée.'
            ], 403);
        }

        $declaration->delete();

        return response()->json(['message' => 'Déclaration supprimée avec succès']);
    }

    /**
     * Liste des impôts avec statut "fait / non fait" pour un contribuable
     */
    public function impotsByContribuable($id_contribuable)
    {
        $contribuable = \App\Models\Contribuable::with('typeImpots')->find($id_contribuable);

        if (!$contribuable) {
            return response()->json(['message' => 'Contribuable introuvable'], 404);
        }

        $declarations = Declaration::where('id_contribuable', $id_contribuable)->get();

        $result = $contribuable->typeImpots->map(function($impot) use ($declarations) {
            $fait = $declarations->where('id_type_impot', $impot->id_type_impot)->isNotEmpty();
            return [
                'id_type_impot' => $impot->id_type_impot,
                'nom' => $impot->nom,
                'fait' => $fait
            ];
        });

        return response()->json($result);
    }

}
