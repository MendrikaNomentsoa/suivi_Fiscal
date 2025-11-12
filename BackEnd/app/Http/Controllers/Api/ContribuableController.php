<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contribuable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ContribuableController extends Controller
{
    /**
     *  Lister tous les contribuables
     * Méthode GET /api/contribuables
     */
    public function index()
    {
        // On récupère tous les contribuables avec leur catégorie associée
        $contribuables = Contribuable::with('categorie')->get();

        return response()->json($contribuables);
    }

    /**
     * Créer un nouveau contribuable
     * Méthode POST /api/contribuables
     */
    public function store(Request $request)
    {
        // Validation des données reçues
        $data = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:contribuables,email',
            'telephone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'date_inscription' => 'required|date',
            'id_Type' => 'required|exists:categories,id_Type', // clé étrangère
        ]);

        // Hachage du mot de passe avant stockage
        $data['password'] = Hash::make($data['password']);

        // Création du contribuable
        $contribuable = Contribuable::create($data);

        return response()->json($contribuable, 201);
    }

    /**
     * Afficher un contribuable spécifique
     * Méthode GET /api/contribuables/{id}
     */
    public function show($id_Contribuable)
    {
        $contribuable = Contribuable::with('categorie')->find($id_Contribuable);

        if (!$contribuable) {
            return response()->json(['message' => 'Contribuable non trouvé'], 404);
        }

        return response()->json($contribuable);
    }

    /**
     * Modifier un contribuable
     * Méthode PUT /api/contribuables/{id}
     */
    public function update(Request $request, $id_Contribuable)
    {
        $contribuable = Contribuable::find($id_Contribuable);

        if (!$contribuable) {
            return response()->json(['message' => 'Contribuable non trouvé'], 404);
        }

        $data = $request->validate([
            'nom' => 'sometimes|string|max:100',
            'prenom' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:contribuables,email,' . $id_Contribuable . ',id_Contribuable',
            'telephone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'id_Type' => 'sometimes|exists:categories,id_Type',
        ]);

        // Si mot de passe envoyé → le hacher
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $contribuable->update($data);

        return response()->json($contribuable);
    }

    /**
     * Supprimer un contribuable
     * Méthode DELETE /api/contribuables/{id}
     */
    public function destroy($id_Contribuable)
    {
        $contribuable = Contribuable::find($id_Contribuable);

        if (!$contribuable) {
            return response()->json(['message' => 'Contribuable non trouvé'], 404);
        }

        $contribuable->delete();

        return response()->json(['message' => 'Contribuable supprimé avec succès']);
    }
}
