<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Echeance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EcheanceController extends Controller
{
    /**
     * Lister toutes les échéances
     */
    public function index()
    {
        $echeances = Echeance::with(['contribuable', 'typeImpot', 'declaration'])->get();
        return response()->json($echeances);
    }

    /**
     * Afficher une seule échéance
     */
    public function show($id)
    {
        $echeance = Echeance::with(['contribuable', 'typeImpot', 'declaration'])->find($id);

        if (!$echeance) {
            return response()->json(['message' => 'Échéance non trouvée'], 404);
        }

        return response()->json($echeance);
    }

    /**
     * Mise à jour d'une échéance (montant, date, paiement)
     */
    public function update(Request $request, $id)
    {
        $echeance = Echeance::find($id);

        if (!$echeance) {
            return response()->json(['message' => 'Échéance non trouvée'], 404);
        }

        $validated = $request->validate([
            'montant_du' => 'sometimes|numeric|min:0',
            'date_limite' => 'sometimes|date',
            'date_paiement' => 'nullable|date'
        ]);

        // Si la date limite est passée ⇒ pénalité + intérêt
        if (isset($validated['date_limite'])) {
            $this->recalculPenaliteInteret($echeance);
        }

        $echeance->update($validated);

        return response()->json([
            'message' => 'Échéance mise à jour',
            'data' => $echeance
        ]);
    }

    /**
     * Calcul pénalité + intérêt selon DGI Madagascar
     */
    private function recalculPenaliteInteret(Echeance $echeance)
    {
        $now = Carbon::now();

        if ($now->lt($echeance->date_limite)) {
            return; // pas de retard
        }

        $mois_retard = $now->diffInMonths($echeance->date_limite);

        // pénalités selon impôt
        if ($echeance->id_type_impot == 1) { // IRSA
            $penalite = 100000;
        } else { // IS / TVA / IR
            $penalite = 20000;
        }

        $interet = ($echeance->montant_du * 0.01) * $mois_retard;

        $echeance->update([
            'penalite' => $penalite,
            'interet' => $interet
        ]);
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
