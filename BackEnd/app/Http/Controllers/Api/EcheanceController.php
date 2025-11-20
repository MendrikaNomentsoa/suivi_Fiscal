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
     * Ajouter une nouvelle échéance avec calcul IRSA + IS
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'montant' => 'required|numeric|min:0',           // Salaire mensuel ou somme imposable IRSA
            'date_limite' => 'required|date',
            'ca_annuel' => 'required|numeric|min:0',        // Chiffre d'affaires pour IS
            'id_Contribuable' => 'required|exists:contribuable,id_contribuable',
        ]);

        /**
         * --------------------------------------
         * 1) CALCUL IRSA
         * --------------------------------------
         * Le calcul est basé sur la loi fiscale :
         * - < 350 000 Ar : 0 Ar
         * - 350 001 → 400 000    (5%)   min. 2 500 Ar
         * - 400 001 → 500 000    (10%)  min. 10 000 Ar
         * - 500 001 → 600 000    (15%)  min. 15 000 Ar
         * - > 600 000            (20%)  min. 20 000 Ar
         */
        $salaire = $data['montant'];
        $irsa = $this->calculIRSA($salaire);


        /**
         * --------------------------------------
         * 2) CALCUL IS (IMPÔT SYNTHÉTIQUE)
         * --------------------------------------
         * - Plafond : CA <= 400 000 000 Ar
         * - Taux IS = 5% du chiffre d'affaires annuel
         */
        $ca = $data['ca_annuel'];

        if ($ca > 400000000) {
            return response()->json([
                'message' => 'Le contribuable dépasse le plafond de l’impôt synthétique (CA maximum : 400 000 000 Ar).'
            ], 422);
        }

        $is = $ca * 0.05;


        /**
         * --------------------------------------
         * 3) ENREGISTREMENT DE L'ÉCHÉANCE
         * --------------------------------------
         */
        $echeance = Echeance::create([
            'montant' => $salaire,
            'date_limite' => $data['date_limite'],
            'penalite' => 0, // pas de pénalité à la création
            'id_contribuable' => $data['id_Contribuable'],
            'irsa' => $irsa,
            'is' => $is,
        ]);

        return response()->json([
            'message' => 'Échéance créée avec calcul IRSA + IS.',
            'data' => $echeance
        ], 201);
    }


    /**
     * -------------------------------
     * FONCTION INTERNE : Calcul IRSA
     * -------------------------------
     * Renvoie le montant IRSA selon barème légal
     */
    private function calculIRSA($salaire)
    {
        if ($salaire <= 350000) {
            return 0;
        }

        if ($salaire <= 400000) {
            return max(($salaire - 350000) * 0.05, 2500);
        }

        if ($salaire <= 500000) {
            return max(($salaire - 400000) * 0.10, 10000);
        }

        if ($salaire <= 600000) {
            return max(($salaire - 500000) * 0.15, 15000);
        }

        // > 600 000 Ar
        return max(($salaire - 600000) * 0.20, 20000);
    }



    /**
     * Afficher une seule échéance
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
     * Modifier une échéance avec recalcul automatique
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

        // Recalcul IRSA si salaire modifié
        if (isset($data['montant'])) {
            $data['irsa'] = $this->calculIRSA($data['montant']);
        }

        // Recalcul IS si CA modifié
        if (isset($data['ca_annuel'])) {

            if ($data['ca_annuel'] > 400000000) {
                return response()->json([
                    'message' => 'Le plafond de l’IS (400 000 000 Ar) est dépassé.'
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
