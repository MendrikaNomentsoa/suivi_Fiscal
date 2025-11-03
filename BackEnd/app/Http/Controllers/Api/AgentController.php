<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    /**
     * Afficher toutes les catégories
     * Méthode GET /api/categories
     */
    public function index()
    {
        // Récupère toutes les catégories depuis la base
        $categories = Categorie::all();

        // Retourne un JSON
        return response()->json($categories);
    }

    /**
     * Ajouter une nouvelle catégorie
     * Méthode POST /api/categories
     */
    public function store(Request $request)
    {
        // Validation des données envoyées par le client (frontend)
        $data = $request->validate([
            'libelle' => 'required|string|max:150|unique:categories,libelle',
            'description' => 'nullable|string',
        ]);

        // Création et enregistrement dans la base
        $categorie = Categorie::create($data);

        // Retourne un JSON + code HTTP 201 (créé avec succès)
        return response()->json($categorie, 201);
    }

    /**
     * Afficher une catégorie précise
     * Méthode GET /api/categories/{id}
     */
    public function show($id)
    {
        // Recherche d'une catégorie par son ID
        $categorie = Categorie::find($id);

        // Si non trouvée → message d’erreur 404
        if (!$categorie) {
            return response()->json(['message' => 'Catégorie non trouvée'], 404);
        }

        // Sinon on la renvoie au format JSON
        return response()->json($categorie);
    }

    /**
     * ✏️ Modifier une catégorie existante
     * Méthode PUT /api/categories/{id}
     */
    public function update(Request $request, $id)
    {
        $categorie = Categorie::find($id);
        if (!$categorie) {
            return response()->json(['message' => 'Catégorie non trouvée'], 404);
        }

        // Validation des champs modifiés
        $data = $request->validate([
            // "sometimes" → ne valide que si la clé existe dans la requête
            'libelle' => 'sometimes|string|max:150|unique:categories,libelle,' . $id . ',id_Type',
            'description' => 'nullable|string',
        ]);

        // Mise à jour
        $categorie->update($data);

        // Retourne la catégorie modifiée
        return response()->json($categorie);
    }

    /**
     * Supprimer une catégorie
     * Méthode DELETE /api/categories/{id}
     */
    public function destroy($id)
    {
        $categorie = Categorie::find($id);
        if (!$categorie) {
            return response()->json(['message' => 'Catégorie non trouvée'], 404);
        }

        // (Optionnel) Empêcher la suppression si la catégorie a des contribuables
        if ($categorie->contribuables()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer : des contribuables sont liés à cette catégorie'
            ], 400);
        }

        // Suppression
        $categorie->delete();

        return response()->json(['message' => 'Catégorie supprimée avec succès']);

    }
}
