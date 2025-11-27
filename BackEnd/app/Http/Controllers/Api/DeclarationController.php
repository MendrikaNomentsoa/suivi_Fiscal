<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Declaration;
use App\Models\Echeance;
use App\Models\TypeImpot;
use App\Models\Contribuable;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class DeclarationController extends Controller
{
    /**
     * Liste de toutes les déclarations
     */
    public function index()
    {
        $declarations = Declaration::with(['typeImpot', 'contribuable', 'echeance'])
            ->orderBy('date_declaration', 'desc')
            ->get();

        return response()->json($declarations);
    }

    /**
     * Ajouter une déclaration
     */
    public function store(Request $request)
    {
        $statutsAutorises = ['validee', 'valide', 'en_attente'];

        $validated = $request->validate([
            'id_type_impot' => 'required|integer|exists:type_impots,id_type_impot',
            'id_contribuable' => 'required|integer|exists:contribuables,id_contribuable',
            'data' => 'required|array',
            'data.remuneration' => 'nullable|numeric|min:0',
            'data.avantages' => 'nullable|numeric|min:0',
            'data.regularisations' => 'nullable|numeric|min:0',
            'data.chiffre_affaires' => 'nullable|numeric|min:0',
            'data.charges' => 'nullable|numeric|min:0',
            'data.autres_revenus' => 'nullable|numeric|min:0',
            'statut' => ['required', 'string', Rule::in($statutsAutorises)]
        ]);

        DB::beginTransaction();
        try {
            // Calcul du montant
            $montant = $this->calculMontant($validated['id_type_impot'], $validated['data']);

            // Création de la déclaration
            $declaration = Declaration::create([
                'id_type_impot' => $validated['id_type_impot'],
                'id_contribuable' => $validated['id_contribuable'],
                'montant' => $montant,
                'date_declaration' => Carbon::now(),
                'statut' => $validated['statut'],
                'statut_paiement' => 'non_paye', // par défaut
                'data' => $validated['data']
            ]);

            $echeance = null;

            if ($validated['statut'] !== 'en_attente') {
                $date_limite = $this->calculDateLimite((int)$validated['id_type_impot']);

                $echeance = Echeance::create([
                    'id_contribuable' => $validated['id_contribuable'],
                    'id_declaration' => $declaration->id_declaration,
                    'id_type_impot' => $validated['id_type_impot'],
                    'montant_du' => $montant,
                    'penalite' => 0,
                    'interet' => 0,
                    'date_limite' => $date_limite
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Déclaration créée avec succès',
                'declaration' => $declaration->load(['typeImpot', 'contribuable', 'echeance']),
                'echeance' => $echeance
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de la déclaration', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la déclaration',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcul du montant selon le type d'impôt
     */
    private function calculMontant($id_type_impot, $data)
    {
        return $id_type_impot == 1 ? $this->calculIRSA($data) : $this->calculIS($data);
    }

    private function calculIRSA(array $data)
    {
        $remuneration = (float)($data['remuneration'] ?? 0);
        $avantages = (float)($data['avantages'] ?? 0);
        $regularisations = (float)($data['regularisations'] ?? 0);

        $base = $remuneration + $avantages;
        $irsa = 0;

        if ($base <= 350000) {
            $irsa = 0;
        } elseif ($base <= 400000) {
            $irsa = ($base - 350000) * 0.05;
        } elseif ($base <= 500000) {
            $irsa = 2500 + ($base - 400000) * 0.10;
        } elseif ($base <= 600000) {
            $irsa = 12500 + ($base - 500000) * 0.15;
        } else {
            $irsa = 27500 + ($base - 600000) * 0.20;
        }

        return round(max(0, $irsa - $regularisations), 2);
    }

    private function calculIS(array $data)
    {
        $benefice = ($data['chiffre_affaires'] ?? 0) + ($data['autres_revenus'] ?? 0) - ($data['charges'] ?? 0);
        return round(max(0, $benefice * 0.20), 2);
    }

    private function calculDateLimite(int $id_type_impot): Carbon
    {
        $now = Carbon::now();
        if ($id_type_impot === 1) return $now->copy()->addMonthNoOverflow()->day(15);

        $may15 = Carbon::create($now->year, 5, 15);
        return $now->lessThanOrEqualTo($may15) ? $may15 : $may15->addYear();
    }

    /**
     * Afficher une déclaration
     */
    public function show($id)
    {
        $declaration = Declaration::with(['typeImpot', 'contribuable', 'echeance'])->find($id);

        if (!$declaration) {
            return response()->json([
                'success' => false,
                'message' => 'Déclaration introuvable'
            ], 404);
        }

        return response()->json(['success' => true, 'declaration' => $declaration]);
    }

    /**
     * Mettre à jour une déclaration
     */
    public function update(Request $request, $id)
    {
        $declaration = Declaration::find($id);
        if (!$declaration) return response()->json(['success' => false, 'message' => 'Déclaration introuvable'], 404);
        if ($declaration->statut === 'validee') return response()->json(['success' => false, 'message' => 'Déclaration validée, non modifiable'], 403);

        $statutsAutorises = ['validee', 'valide', 'en_attente'];
        $validated = $request->validate([
            'data' => 'sometimes|array',
            'data.remuneration' => 'nullable|numeric|min:0',
            'data.avantages' => 'nullable|numeric|min:0',
            'data.regularisations' => 'nullable|numeric|min:0',
            'data.chiffre_affaires' => 'nullable|numeric|min:0',
            'data.charges' => 'nullable|numeric|min:0',
            'data.autres_revenus' => 'nullable|numeric|min:0',
            'statut' => ['sometimes', 'string', Rule::in($statutsAutorises)]
        ]);

        DB::beginTransaction();
        try {
            if (isset($validated['data'])) {
                $montant = $this->calculMontant($declaration->id_type_impot, $validated['data']);
                $validated['montant'] = $montant;

                $echeance = $declaration->echeance;
                if ($echeance) {
                    $echeance->update(['montant_du' => $montant]);
                } elseif (($validated['statut'] ?? $declaration->statut) !== 'en_attente') {
                    Echeance::create([
                        'id_contribuable' => $declaration->id_contribuable,
                        'id_declaration' => $declaration->id_declaration,
                        'id_type_impot' => $declaration->id_type_impot,
                        'montant_du' => $montant,
                        'penalite' => 0,
                        'interet' => 0,
                        'date_limite' => $this->calculDateLimite($declaration->id_type_impot)
                    ]);
                }
            }

            $declaration->update($validated);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Déclaration mise à jour',
                'declaration' => $declaration->fresh(['typeImpot', 'contribuable', 'echeance'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur mise à jour déclaration', ['message'=>$e->getMessage()]);
            return response()->json(['success'=>false, 'message'=>'Erreur lors de la mise à jour','error'=>$e->getMessage()], 500);
        }
    }

    /**
     * Supprimer une déclaration
     */
    public function destroy($id)
    {
        $declaration = Declaration::find($id);
        if (!$declaration) return response()->json(['success'=>false,'message'=>'Déclaration introuvable'],404);
        if ($declaration->statut === 'validee') return response()->json(['success'=>false,'message'=>'Déclaration validée, non supprimable'],403);

        DB::beginTransaction();
        try {
            $declaration->echeance()->delete();
            $declaration->delete();
            DB::commit();

            return response()->json(['success'=>true,'message'=>'Déclaration supprimée']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur suppression déclaration',['message'=>$e->getMessage()]);
            return response()->json(['success'=>false,'message'=>'Erreur lors de la suppression','error'=>$e->getMessage()],500);
        }
    }

    /**
     * Liste des impôts avec statut de paiement pour un contribuable
     */
    /**
 * Liste des déclarations d'un contribuable avec toutes les relations
 */
public function impotsByContribuable($id_contribuable)
{
    $contribuable = Contribuable::find($id_contribuable);

    if (!$contribuable) {
        return response()->json([
            'success' => false,
            'message' => 'Contribuable introuvable'
        ], 404);
    }

    // Récupérer toutes les déclarations avec leurs relations
    $declarations = Declaration::where('id_contribuable', $id_contribuable)
        ->with(['typeImpot', 'echeance'])
        ->whereIn('statut', ['valide', 'validee'])
        ->orderBy('date_declaration', 'desc')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $declarations
    ]);
}

    /**
     * Statistiques des déclarations
     */
    public function statistiques($id_contribuable)
    {
        $declarations = Declaration::where('id_contribuable', $id_contribuable)->get();
        $stats = [
            'total_declarations' => $declarations->count(),
            'montant_total' => $declarations->sum('montant'),
            'declarations_validees' => $declarations->where('statut','validee')->count(),
            'declarations_valide' => $declarations->where('statut','valide')->count(),
            'declarations_en_attente' => $declarations->where('statut','en_attente')->count(),
            'par_type_impot' => []
        ];

        foreach ($declarations->groupBy('id_type_impot') as $typeId => $decs) {
            $typeImpot = TypeImpot::find($typeId);
            $stats['par_type_impot'][] = [
                'type' => $typeImpot->nom ?? 'Inconnu',
                'nombre' => $decs->count(),
                'montant_total' => $decs->sum('montant')
            ];
        }

        return response()->json($stats);
    }

    /**
     * Impôts non déclarés pour un contribuable
     */
    public function impotsNonDeclares($idContribuable)
    {
        $impots = TypeImpot::all();

        return $impots->map(function($impot) use($idContribuable){
            $dejaDeclare = Declaration::where('id_contribuable',$idContribuable)
                                      ->where('id_type_impot',$impot->id_type_impot)
                                      ->exists();
            return [
                'id'=>$impot->id_type_impot,
                'nom'=>$impot->nom,
                'a_valider'=>!$dejaDeclare
            ];
        })->filter(fn($i)=>$i['a_valider'])->values();
    }
}
