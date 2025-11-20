<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Declaration;
use Illuminate\Http\Request;

class DeclarationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $declarations = Declaration::with(['type_impots','contribuables'])->get();

        return response()->json($declarations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
        'id_type_impot' => 'required|exists:type_impots,id_type_impot',
        'id_contribuable' => 'required|exists:contribuable,id_contribuable',
        'montant' => 'required|numeric|min:0',
        'date_declaration'=> 'nullable|date',
        'statut' => 'required|in:brouillon,validee'
        ]);

        $declaration = Declaration::create($validated);

        return response()->json([
            'message' => 'Déclaration créée avec succès',
            'data' => $declaration
            ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $declaration = Declaration::with(['typeImpot', 'contribuable'])->find($id);

        if (!$declaration) {
        return response()->json(['message' => 'Déclaration introuvable'], 404);
        }

        return response()->json($declaration);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $declaration = Declaration::find($id);

        if (!$declaration) {
        return response()->json(['message' => 'Déclaration introuvable'], 404);
        }

        $validated = $request->validate([
        'id_type_impot' => 'sometimes|exists:type_impots,id_type_impot',
        'id_contribuable' => 'sometimes|exists:contribuable,id_contribuable',
        'montant' => 'sometimes|numeric|min:0',
        'date_declaration'=> 'nullable|date',
        'statut' => 'sometimes|in:brouillon,validee'
        ]);

        $declaration->update($validated);

        return response()->json([
        'message' => 'Déclaration mise à jour',
        'data' => $declaration
        ]);
    }

    /**
     * Remove the specified resource from storage.
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
