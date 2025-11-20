<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contribuable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ContribuableController extends Controller
{
    /**
     * Lister tous les contribuables
     * GET /api/contribuables
     */
    public function index()
    {
        // Pas de relation categorie
        $contribuables = Contribuable::all();

        return response()->json($contribuables);
    }

    /**
     * Créer un nouveau contribuable
     * POST /api/contribuables
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:contribuable,email',
            'telephone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'date_inscription' => 'required|date',
        ]);

        // Hash mot de passe
        $data['password'] = Hash::make($data['password']);

        $contribuable = Contribuable::create($data);

        return response()->json($contribuable, 201);
    }

    /**
     * Afficher un contribuable
     * GET /api/contribuables/{id}
     */
    public function show($id_contribuable)
    {
        $contribuable = Contribuable::find($id_contribuable);

        if (!$contribuable) {
            return response()->json(['message' => 'Contribuable non trouvé'], 404);
        }

        return response()->json($contribuable);
    }

    /**
     * Modifier un contribuable
     * PUT /api/contribuables/{id}
     */
    public function update(Request $request, $id_contribuable)
    {
        $contribuable = Contribuable::find($id_contribuable);

        if (!$contribuable) {
            return response()->json(['message' => 'Contribuable non trouvé'], 404);
        }

        $data = $request->validate([
            'nom' => 'sometimes|string|max:100',
            'prenom' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:contribuable,email,' . $id_contribuable . ',id_contribuable',
            'telephone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'date_inscription' => 'sometimes|date',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $contribuable->update($data);

        return response()->json($contribuable);
    }

    /**
     * Supprimer un contribuable
     * DELETE /api/contribuables/{id}
     */
    public function destroy($id_contribuable)
    {
        $contribuable = Contribuable::find($id_contribuable);

        if (!$contribuable) {
            return response()->json(['message' => 'Contribuable non trouvé'], 404);
        }

        $contribuable->delete();

        return response()->json(['message' => 'Contribuable supprimé avec succès']);
    }
}
