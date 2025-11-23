<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Declaration;
use App\Models\Echeance;
use App\Models\TypeImpot;
use App\Models\Contribuable;
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
        'data' => 'required|array', // les lignes du formulaire
        'statut' => 'required|in:brouillon,validee'
    ]);

    $montant = 0;

    if ($validated['id_type_impot'] == 1) { // IRSA
        $remuneration = $validated['data'][10] ?? 0;
        $regularisations = $validated['data'][19] ?? 0;
        $avantages = $validated['data'][5] ?? 0;

        // Calcul simplifié IRSA selon tranches
        $base = $remuneration + $avantages;
        if ($base <= 350000) $montant = 0;
        elseif ($base <= 400000) $montant = ($base - 350000) * 0.05;
        elseif ($base <= 500000) $montant = ($base - 400000) * 0.1 + 2500;
        elseif ($base <= 600000) $montant = ($base - 500000) * 0.15 + 12500;
        else $montant = ($base - 600000) * 0.2 + 27500;

        $montant -= $regularisations;
    } else { // IS
        $chiffreAffaires = $validated['data'][1] ?? 0;
        $benefice = $chiffreAffaires * 0.2; // exemple
        $montant = $benefice * 0.15; // taux IS
    }

    $declaration = Declaration::create([
        'id_type_impot' => $validated['id_type_impot'],
        'id_contribuable' => $validated['id_contribuable'],
        'montant' => $montant,
        'date_declaration' => now(),
        'statut' => $validated['statut']
    ]);

    // date limite et échéance
    $date = now();
    $date_limite = $validated['id_type_impot'] == 1 ? $date->copy()->addMonth()->day(15)
                   : Carbon::create($date->year + 1, 5, 15);

    Echeance::create([
        'id_contribuable' => $validated['id_contribuable'],
        'id_declaration' => $declaration->id_declaration,
        'id_type_impot' => $validated['id_type_impot'],
        'montant_du' => $montant,
        'penalite' => 0,
        'interet' => 0,
        'date_limite' => $date_limite
    ]);

    return response()->json([
        'message' => 'Déclaration créée avec succès',
        'declaration' => $declaration
    ], 201);
}

    /**
     * Calcul du montant selon le type d'impôt
     */
    private function calculMontant($id_type_impot, $data)
    {
        if ($id_type_impot == 1) {
            return $this->calculIRSA($data);
        } else {
            return $this->calculIS($data);
        }
    }

    /**
     * Calcul IRSA selon tranches
     */
    private function calculIRSA($data)
    {
        $remuneration = $data['remuneration_nette'] ?? 0;
        $avantages = $data['avantages_en_nature'] ?? 0;
        $total = $remuneration + $avantages;

        $irsa = 0;
        if ($total <= 350000) {
            $irsa = 0;
        } elseif ($total <= 400000) {
            $irsa = ($total - 350000) * 0.05;
        } elseif ($total <= 500000) {
            $irsa = (400000 - 350000) * 0.05 + ($total - 400000) * 0.10;
        } elseif ($total <= 600000) {
            $irsa = (400000 - 350000) * 0.05 + (500000 - 400000) * 0.10 + ($total - 500000) * 0.15;
        } else {
            $irsa = (400000 - 350000) * 0.05 + (500000 - 400000) * 0.10 + (600000 - 500000) * 0.15 + ($total - 600000) * 0.20;
        }

        return $irsa;
    }

    /**
     * Calcul IS simple exemple
     */
    private function calculIS($data)
    {
        $chiffre_affaires = $data['chiffre_affaires'] ?? 0;
        $benefice = $chiffre_affaires * 0.2; // exemple calcul bénéfice imposable
        $is = $benefice * 0.15; // taux IS
        return $is;
    }

    /**
     * Afficher une déclaration
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
     */
    public function update(Request $request, $id)
    {
        $declaration = Declaration::find($id);

        if (!$declaration) {
            return response()->json(['message' => 'Déclaration introuvable'], 404);
        }

        if ($declaration->statut === 'validee') {
            return response()->json([
                'message' => 'Cette déclaration est validée et ne peut plus être modifiée.'
            ], 403);
        }

        $validated = $request->validate([
            'id_type_impot' => 'sometimes|exists:type_impots,id_type_impot',
            'id_contribuable' => 'sometimes|exists:contribuables,id_contribuable',
            'data' => 'sometimes|array',
            'statut' => 'sometimes|in:brouillon,validee'
        ]);

        // Recalculer le montant si les données changent
        if (isset($validated['data'])) {
            $validated['montant'] = $this->calculMontant($validated['id_type_impot'] ?? $declaration->id_type_impot, $validated['data']);
        }

        $declaration->update($validated);

        return response()->json([
            'message' => 'Déclaration mise à jour avec succès',
            'declaration' => $declaration
        ]);
    }

    /**
     * Supprimer une déclaration
     */
    public function destroy($id)
    {
        $declaration = Declaration::find($id);

        if (!$declaration) {
            return response()->json(['message' => 'Déclaration introuvable'], 404);
        }

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
        $contribuable = Contribuable::with('typeImpots')->find($id_contribuable);

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
