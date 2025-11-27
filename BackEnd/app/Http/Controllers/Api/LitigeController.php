<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Litige;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LitigeController extends Controller
{
    /**
     * Afficher tous les litiges
     */
    public function index(): JsonResponse
    {
        try {
            $litiges = Litige::with('contribuable')
                ->orderBy('date_ouverture', 'desc')
                ->get();

            return response()->json($litiges, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors du chargement des litiges',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer un nouveau litige
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'sujet' => 'required|string|max:255',
                'description' => 'nullable|string',
                'id_Contribuable' => 'required|exists:contribuables,id_Contribuable',
                'statut' => 'nullable|in:en_attente,en_cours,resolu,rejete'
            ]);

            // Valeurs par défaut
            $validated['statut'] = $validated['statut'] ?? 'en_attente';
            $validated['date_ouverture'] = now();

            $litige = Litige::create($validated);

            return response()->json($litige, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Données invalides',
                'message' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la création du litige',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher un litige spécifique
     */
    public function show(string $id): JsonResponse
    {
        try {
            $litige = Litige::with('contribuable')->findOrFail($id);
            return response()->json($litige, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Litige non trouvé'
            ], 404);
        }
    }

    /**
     * Mettre à jour un litige
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $litige = Litige::findOrFail($id);

            $validated = $request->validate([
                'sujet' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'statut' => 'sometimes|in:en_attente,en_cours,resolu,rejete'
            ]);

            $litige->update($validated);

            return response()->json($litige, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Litige non trouvé'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la mise à jour',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un litige
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $litige = Litige::findOrFail($id);
            $litige->delete();

            return response()->json([
                'message' => 'Litige supprimé avec succès'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Litige non trouvé'
            ], 404);
        }
    }
}
