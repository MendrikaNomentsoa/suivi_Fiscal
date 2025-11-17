<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Echeance;
use Illuminate\Http\Request;

class EcheanceController extends Controller
{
    /**
     * Afficher toutes les échéances
     */
    public function index()
    {
        $echeances = Echeance::with('contribuable')->get();
        return response()->json($echeances);
    }

    /**
     * Ajouter une nouvelle échéance (calcul IRSA + IS)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'montant' => 'required|numeric|min:0',           // salaire ou montant imposable
            'date_limite' => 'required|date',
            'ca_annuel' => 'required|numeric|min:0',        // CA pour calcul IS
            'id_Contribuable' => 'required|exists:contribuables,id_Contribuable',
        ]);

        /**
         * ---------------------------
         * 1) CALCUL IRSA
         * ---------------------------
         */
        $montant = $data['montant'];
        $irsa = $this->calculIRSA($montant);

        /**
         * ---------------------------
         * 2) CALCUL IS (IMPOT SYNTÉTIQUE)
         * ---------------------------
         */
        $ca = $data['ca_annuel'];

        if ($ca > 400000000) {
            return response()->json([
                'message' => 'Le contribuable dépasse le plafond de l’impôt synthétique (20 000 000 Ar).'
            ], 422);
        }

        // IS = 5% du chiffre d’affaires
        $is = $ca * 0.05;

        /**
         * ---------------------------
         * 3) Enregistrer l’échéance
         * ---------------------------
         */
        $echeance = Echeance::create([
            'montant' => $montant,
            'date_limite' => $data['date_limite'],
            'penalite' => 0,
            'id_Contribuable' => $data['id_Contribuable'],
            'irsa' => $irsa,
            'is' => $is
        ]);

        return response()->json([
            'message' => 'Échéance créée avec calcul IRSA + IS.',
            'data' => $echeance
        ], 201);
    }

    /**
     * Fonction interne : Calcul IRSA Madagascar
     */
    private function calculIRSA($salaire)
    {
        if ($salaire <= 350000) return 0;

        if ($salaire <= 400000) return max(($salaire - 350000) * 0.05, 2500);

        if ($salaire <= 500000) return max(($salaire - 400000) * 0.10, 10000);

        if ($salaire <= 600000) return max(($salaire - 500000) * 0.15, 15000);

        if ($salaire > 600000) return max(($salaire - 600000) * 0.20, 20000);

        return 0;
    }

    /**
     * Afficher une échéance
     */
    public function show($id)
    {
        $echeance = Echeance::with('contribuable')->find($id);

        if (!$echeance) {
            return response()->json(['message' => 'Échéance non trouvée'], 404);
        }

        return response()->json($echeance);
    }

    /**
     * Modifier une échéance
     */
    public function update(Request $request, $id)
    {
        $echeance = Echeance::find($id);

        if (!$echeance) {
            return response()->json(['message' => 'Échéance non trouvée'], 404);
        }

        $data = $request->validate([
            'montant' => 'sometimes|numeric|min:0',
            'date_limite' => 'sometimes|date',
            'ca_annuel' => 'sometimes|numeric|min:0',
        ]);

        // recalcul si montant changé
        if (isset($data['montant'])) {
            $data['irsa'] = $this->calculIRSA($data['montant']);
        }

        // recalcul si CA changé
        if (isset($data['ca_annuel'])) {
            if ($data['ca_annuel'] > 400000000) {
                return response()->json([
                    'message' => 'Le contribuable dépasse le plafond de l’impôt synthétique.'
                ], 422);
            }

            $data['is'] = $data['ca_annuel'] * 0.05;
        }

        $echeance->update($data);

        return response()->json($echeance);
    }

    /**
     * Supprimer une échéance
     */
    public function destroy($id)
    {
        $echeance = Echeance::find($id);

        if (!$echeance) {
            return response()->json(['message' => 'Échéance non trouvée'], 404);
        }

        $echeance->delete();
        return response()->json(['message' => 'Échéance supprimée avec succès']);
    }
}
