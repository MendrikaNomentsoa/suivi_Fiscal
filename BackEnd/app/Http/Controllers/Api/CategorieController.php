<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     *  Méthode GET
     */
    public function index()
    {
        $categorie = Categorie::all();  // Récupère toutes les catégories depuis la base

        return response()->json($categorie);
    }

    /**
     * Store a newly created resource in storage.
     *  Méthode POST
     */
    public function store(Request $request)
    {
        // Va$id_Typeation des données envoyées par le client (frontend)
        $data = $request->validate([
            'libelle' => 'required|string|max:150|unique:categories,libelle',
            'design' => 'nullable|string',
        ]);

         // Création et enregistrement dans la base
         $categorie = Categorie::create($data);

         return response()->json($categorie, 201);// Retourne un JSON + code HTTP 201 (créé avec succès)
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_Type_Type)
    {
         // Recherche d'une catégorie par son$id_Type
        $categorie = Categorie::find($id_Type_Type);

        if(!$categorie){
            return response()->json(['message' => 'categorie non trouver'], 404);
        }

        return response()->json($categorie);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id_Type_Type)
    {

         // Recherche d'une catégorie par son$id_Type
        $categorie = Categorie::find($id_Type_Type);

        if(!$categorie){
            return response()->json(['message' => 'categorie non trouver'], 404);
        }

        // Va$id_Typeation des données envoyées par le client (frontend)
        $data = $request->validate([
            'libelle' => 'required|string|max:150|unique:categories,libelle',
            'design' => 'nullable|string',
        ]);

        $categorie->update($data);

        return response()->json($categorie);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_Type)
    {
        $categorie = Categorie::find($id_Type);
        if (!$categorie) {
            return response()->json(['message' => 'Catégorie non trouvée'], 404);
        }

        //Empêcher la suppression si la catégorie a des contribuables
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
