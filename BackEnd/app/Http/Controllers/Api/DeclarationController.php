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
     * GET /api/declarations
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
     * POST /api/declarations
     */
    /**
 * Ajouter une déclaration
 * POST /api/declarations
 */
    /**
 * Ajouter une déclaration
 * POST /api/declarations
 */
    public function store(Request $request)
    {
        // Statuts autorisés — harmonisés
        $statutsAuthorises = ['brouillon', 'valide', 'validee'];

        try {
            $validated = $request->validate([
                'id_type_impot' => 'required|exists:type_impots,id_type_impot',
                'id_contribuable' => 'required|exists:contribuables,id_contribuable',
                'data' => 'required|array',
                'data.remuneration' => 'nullable|numeric|min:0',
                'data.avantages' => 'nullable|numeric|min:0',
                'data.regularisations' => 'nullable|numeric|min:0',
                'data.chiffre_affaires' => 'nullable|numeric|min:0',
                'data.charges' => 'nullable|numeric|min:0',
                'data.autres_revenus' => 'nullable|numeric|min:0',
                'statut' => ['required', Rule::in($statutsAuthorises)]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Log des données reçues pour débogage
            Log::info('Données reçues pour déclaration', [
                'id_type_impot' => $validated['id_type_impot'],
                'data' => $validated['data']
            ]);

            // Calcul du montant selon le type d'impôt
            $montant = $this->calculMontant($validated['id_type_impot'], $validated['data']);

            Log::info('Montant calculé', ['montant' => $montant]);

            // Créer la déclaration avec les données
            $declaration = Declaration::create([
                'id_type_impot' => $validated['id_type_impot'],
                'id_contribuable' => $validated['id_contribuable'],
                'montant' => $montant,
                'date_declaration' => Carbon::now(),
                'statut' => $validated['statut'],
                'data' => $validated['data']
            ]);

            // Création de l'échéance : seulement si statut différent de brouillon
            if ($validated['statut'] !== 'brouillon') {
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
            } else {
                $echeance = null;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Déclaration créée avec succès',
                'declaration' => $declaration->load(['typeImpot', 'contribuable', 'echeance']),
                'echeance' => $echeance
            ], 201);

        } catch (\InvalidArgumentException $e) {
            DB::rollBack();
            Log::error('Erreur de validation des données', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Données invalides: ' . $e->getMessage()
            ], 400);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de la déclaration', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la déclaration',
                'error' => $e->getMessage(),
                'debug' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
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
     * Calcul IRSA selon tranches progressives
     * Attendu : $data['remuneration'], $data['avantages'], $data['regularisations']
     */
    private function calculIRSA(array $data)
    {
        // Récupération des données (clés explicites)
        $remuneration = (float)($data['remuneration'] ?? 0);
        $avantages = (float)($data['avantages'] ?? 0);
        $regularisations = (float)($data['regularisations'] ?? 0);

        // Base imposable
        $base = $remuneration + $avantages;

        // Calcul IRSA selon les tranches progressives
        $irsa = 0.0;

        if ($base <= 350000) {
            $irsa = 0;
        } elseif ($base <= 400000) {
            // 5% sur la portion entre 350 000 et 400 000
            $irsa = ($base - 350000) * 0.05;
        } elseif ($base <= 500000) {
            // 5% sur 50 000 = 2 500
            // 10% sur portion entre 400 000 et 500 000
            $irsa = 2500 + ($base - 400000) * 0.10;
        } elseif ($base <= 600000) {
            // 2 500 + 10 000 + 15% sur portion 500k-600k
            $irsa = 12500 + ($base - 500000) * 0.15;
        } else {
            // 2 500 + 10 000 + 15 000 + 20% sur le reste
            $irsa = 27500 + ($base - 600000) * 0.20;
        }

        // Soustraire les régularisations
        $montantFinal = max(0, $irsa - $regularisations);

        return round($montantFinal, 2);
    }

    /**
     * Calcul IS (Impôt sur les Sociétés)
     * Attendu : $data['chiffre_affaires'], $data['charges'], $data['autres_revenus']
     */
    private function calculIS(array $data)
    {
        $chiffreAffaires = (float)($data['chiffre_affaires'] ?? 0);
        $charges = (float)($data['charges'] ?? 0);
        $autresRevenus = (float)($data['autres_revenus'] ?? 0);

        // Calcul du bénéfice imposable
        $benefice = $chiffreAffaires + $autresRevenus - $charges;

        // Calcul IS (15% du bénéfice)
        $is = max(0, $benefice * 0.15);

        return round($is, 2);
    }

    /**
     * Calculer la date limite selon le type d'impôt
     * - id_type_impot == 1 : IRSA -> 15 du mois suivant
     * - sinon : IS -> 15 mai de l'année courante ou suivante (si déjà passé)
     */
    private function calculDateLimite(int $id_type_impot): Carbon
    {
        $now = Carbon::now();

        if ($id_type_impot === 1) {
            // 15 du mois suivant
            $nextMonth = $now->copy()->addMonthNoOverflow();
            // remettre au jour 15
            return $nextMonth->day(15);
        }

        // Pour IS : 15 mai
        $may15ThisYear = Carbon::create($now->year, 5, 15, 0, 0, 0);

        if ($now->lessThanOrEqualTo($may15ThisYear)) {
            return $may15ThisYear;
        }

        // si déjà passé -> 15 mai de l'année suivante
        return $may15ThisYear->addYear();
    }

    /**
     * Afficher une déclaration
     * GET /api/declarations/{id}
     */
    public function show($id)
    {
        $declaration = Declaration::with(['typeImpot', 'contribuable', 'echeance'])
            ->find($id);

        if (!$declaration) {
            return response()->json([
                'success' => false,
                'message' => 'Déclaration introuvable'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'declaration' => $declaration
        ]);
    }

    /**
     * Modifier une déclaration
     * PUT /api/declarations/{id}
     */
    public function update(Request $request, $id)
    {
        $declaration = Declaration::find($id);

        if (!$declaration) {
            return response()->json([
                'success' => false,
                'message' => 'Déclaration introuvable'
            ], 404);
        }

        if ($declaration->statut === 'validee') {
            return response()->json([
                'success' => false,
                'message' => 'Cette déclaration est validée et ne peut plus être modifiée.'
            ], 403);
        }

        $statutsAuthorises = ['brouillon', 'valide', 'validee'];

        $validated = $request->validate([
            'data' => 'sometimes|array',
            'data.remuneration' => 'nullable|numeric',
            'data.avantages' => 'nullable|numeric',
            'data.regularisations' => 'nullable|numeric',
            'data.chiffre_affaires' => 'nullable|numeric',
            'data.charges' => 'nullable|numeric',
            'data.autres_revenus' => 'nullable|numeric',
            'statut' => ['sometimes', Rule::in($statutsAuthorises)]
        ]);

        DB::beginTransaction();

        try {
            // Recalculer le montant si les données changent
            if (isset($validated['data'])) {
                $montant = $this->calculMontant($declaration->id_type_impot, $validated['data']);
                $validated['montant'] = $montant;

                // Mettre à jour l'échéance associée (ou la créer si elle n'existe pas et statut != brouillon)
                $echeance = $declaration->echeance;
                if ($echeance) {
                    $echeance->update(['montant_du' => $montant]);
                } else {
                    if (($validated['statut'] ?? $declaration->statut) !== 'brouillon') {
                        $date_limite = $this->calculDateLimite($declaration->id_type_impot);
                        Echeance::create([
                            'id_contribuable' => $declaration->id_contribuable,
                            'id_declaration' => $declaration->id_declaration,
                            'id_type_impot' => $declaration->id_type_impot,
                            'montant_du' => $montant,
                            'penalite' => 0,
                            'interet' => 0,
                            'date_limite' => $date_limite
                        ]);
                    }
                }
            }

            // Mettre à jour le model
            $declaration->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Déclaration mise à jour avec succès',
                'declaration' => $declaration->fresh(['typeImpot', 'contribuable', 'echeance'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer une déclaration
     * DELETE /api/declarations/{id}
     */
    public function destroy($id)
    {
        $declaration = Declaration::find($id);

        if (!$declaration) {
            return response()->json([
                'success' => false,
                'message' => 'Déclaration introuvable'
            ], 404);
        }

        if ($declaration->statut === 'validee') {
            return response()->json([
                'success' => false,
                'message' => 'Une déclaration validée ne peut pas être supprimée.'
            ], 403);
        }

        DB::beginTransaction();

        try {
            // Supprimer l'échéance associée
            $declaration->echeance()->delete();

            $declaration->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Déclaration supprimée avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Liste des impôts avec statut "fait / non fait" pour un contribuable
     * GET /api/impots/{id_contribuable}
     */
    public function impotsByContribuable($id_contribuable)
    {
        $contribuable = Contribuable::with('typeImpots')->find($id_contribuable);

        if (!$contribuable) {
            return response()->json([
                'success' => false,
                'message' => 'Contribuable introuvable'
            ], 404);
        }

        $declarations = Declaration::where('id_contribuable', $id_contribuable)->get();

        $result = $contribuable->typeImpots->map(function($impot) use ($declarations) {
            $fait = $declarations->where('id_type_impot', $impot->id_type_impot)->isNotEmpty();
            return [
                'id_type_impot' => $impot->id_type_impot,
                'nom' => $impot->nom,
                'taux' => $impot->taux,
                'fait' => $fait
            ];
        });

        return response()->json($result);
    }

    /**
     * Statistiques pour un contribuable
     * GET /api/declarations/stats/{id_contribuable}
     */
    public function statistiques($id_contribuable)
    {
        $declarations = Declaration::byContribuable($id_contribuable)->get();

        $stats = [
            'total_declarations' => $declarations->count(),
            'montant_total' => $declarations->sum('montant'),
            'declarations_validees' => $declarations->where('statut', 'validee')->count(),
            'declarations_brouillon' => $declarations->where('statut', 'brouillon')->count(),
            'par_type_impot' => []
        ];

        // Statistiques par type d'impôt
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
}
