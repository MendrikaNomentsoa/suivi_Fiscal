<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Echeance;
use App\Models\Contribuable;
use App\Models\Declaration;
use App\Models\TypeImpot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EcheanceController extends Controller
{
    /**
     * Afficher la liste des échéances
     */
    public function index(Request $request)
    {
        $query = Echeance::with(['contribuable', 'declaration', 'typeImpot']);

        // Filtres
        if ($request->filled('statut')) {
            switch ($request->statut) {
                case 'en_retard':
                    $query->enRetard();
                    break;
                case 'payee':
                    $query->payees();
                    break;
                case 'a_venir':
                    $query->aVenir();
                    break;
            }
        }

        if ($request->filled('id_contribuable')) {
            $query->where('id_contribuable', $request->id_contribuable);
        }

        if ($request->filled('id_type_impot')) {
            $query->where('id_type_impot', $request->id_type_impot);
        }

        if ($request->filled('date_debut')) {
            $query->where('date_limite', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->where('date_limite', '<=', $request->date_fin);
        }

        $echeances = $query->orderBy('date_limite', 'desc')->paginate(15);

        // Calculer les pénalités pour chaque échéance
        $echeances->each(function ($echeance) {
            $echeance->calcul_penalites = $echeance->calculerPenalitesEtInterets();
        });

        return view('echeances.index', compact('echeances'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $contribuables = Contribuable::orderBy('nom')->get();
        $declarations = Declaration::orderBy('created_at', 'desc')->get();
        $typesImpot = TypeImpot::all();

        return view('echeances.create', compact('contribuables', 'declarations', 'typesImpot'));
    }

    /**
     * Enregistrer une nouvelle échéance
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_contribuable' => 'required|exists:contribuables,id_contribuable',
            'id_declaration' => 'nullable|exists:declarations,id_declaration',
            'id_type_impot' => 'required|exists:type_impots,id_type_impot',
            'montant_du' => 'required|numeric|min:0',
            'date_limite' => 'required|date',
            'statut' => 'nullable|string|max:50'
        ]);

        $echeance = Echeance::create($validated);

        return redirect()
            ->route('echeances.show', $echeance->id_echeance)
            ->with('success', 'Échéance créée avec succès.');
    }

    /**
     * Afficher les détails d'une échéance
     */
    public function show(Echeance $echeance)
    {
        $echeance->load(['contribuable', 'declaration', 'typeImpot']);
        $calcul = $echeance->calculerPenalitesEtInterets();

        return view('echeances.show', compact('echeance', 'calcul'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Echeance $echeance)
    {
        $contribuables = Contribuable::orderBy('nom')->get();
        $declarations = Declaration::orderBy('created_at', 'desc')->get();
        $typesImpot = TypeImpot::all();

        return view('echeances.edit', compact('echeance', 'contribuables', 'declarations', 'typesImpot'));
    }

    /**
     * Mettre à jour une échéance
     */
    public function update(Request $request, Echeance $echeance)
    {
        $validated = $request->validate([
            'id_contribuable' => 'required|exists:contribuables,id_contribuable',
            'id_declaration' => 'nullable|exists:declarations,id_declaration',
            'id_type_impot' => 'required|exists:type_impots,id_type_impot',
            'montant_du' => 'required|numeric|min:0',
            'date_limite' => 'required|date',
            'statut' => 'nullable|string|max:50'
        ]);

        $echeance->update($validated);

        return redirect()
            ->route('echeances.show', $echeance->id_echeance)
            ->with('success', 'Échéance mise à jour avec succès.');
    }

    /**
     * Supprimer une échéance
     */
    public function destroy(Echeance $echeance)
    {
        $echeance->delete();

        return redirect()
            ->route('echeances.index')
            ->with('success', 'Échéance supprimée avec succès.');
    }

    /**
     * Marquer une échéance comme payée
     */
    public function marquerPayee(Request $request, Echeance $echeance)
    {
        $validated = $request->validate([
            'date_paiement' => 'required|date',
            'appliquer_penalites' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            // Appliquer les pénalités si demandé
            if ($request->appliquer_penalites && $echeance->estEnRetard()) {
                $echeance->appliquerPenalites();
            }

            // Marquer comme payée
            $echeance->date_paiement = $validated['date_paiement'];
            $echeance->statut = 'payee';
            $echeance->save();

            DB::commit();

            return redirect()
                ->route('echeances.show', $echeance->id_echeance)
                ->with('success', 'Échéance marquée comme payée.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors du paiement : ' . $e->getMessage());
        }
    }

    /**
     * Calculer et appliquer les pénalités
     */
    public function appliquerPenalites(Echeance $echeance)
    {
        if (!$echeance->estEnRetard()) {
            return back()->with('error', 'Cette échéance n\'est pas en retard.');
        }

        $calcul = $echeance->appliquerPenalites();

        return back()->with('success', sprintf(
            'Pénalités appliquées : %s Ar de pénalité + %s Ar d\'intérêts (%d mois de retard)',
            number_format($calcul['penalite'], 2),
            number_format($calcul['interet'], 2),
            $calcul['mois_retard']
        ));
    }

    /**
     * Tableau de bord des échéances
     */
    public function dashboard()
    {
        $stats = [
            'total' => Echeance::count(),
            'en_retard' => Echeance::enRetard()->count(),
            'payees' => Echeance::payees()->count(),
            'a_venir' => Echeance::aVenir()->count(),
            'montant_total_du' => Echeance::whereNull('date_paiement')->sum('montant_du'),
            'montant_retard' => Echeance::enRetard()->sum('montant_du')
        ];

        // Échéances en retard récentes
        $echeancesRetard = Echeance::enRetard()
            ->with(['contribuable', 'typeImpot'])
            ->orderBy('date_limite', 'asc')
            ->limit(10)
            ->get();

        // Calcul des pénalités pour les échéances en retard
        $totalPenalites = 0;
        $totalInterets = 0;
        foreach ($echeancesRetard as $echeance) {
            $calcul = $echeance->calculerPenalitesEtInterets();
            $totalPenalites += $calcul['penalite'];
            $totalInterets += $calcul['interet'];
        }

        $stats['penalites_potentielles'] = $totalPenalites;
        $stats['interets_potentiels'] = $totalInterets;

        // Échéances à venir (30 prochains jours)
        $echeancesProchaines = Echeance::aVenir()
            ->with(['contribuable', 'typeImpot'])
            ->where('date_limite', '<=', Carbon::now()->addDays(30))
            ->orderBy('date_limite', 'asc')
            ->get();

        return view('echeances.dashboard', compact('stats', 'echeancesRetard', 'echeancesProchaines'));
    }

    /**
     * Rapport des échéances par contribuable
     */
    public function rapportContribuable($id_contribuable)
    {
        $contribuable = Contribuable::findOrFail($id_contribuable);

        $echeances = Echeance::where('id_contribuable', $id_contribuable)
            ->with(['declaration', 'typeImpot'])
            ->orderBy('date_limite', 'desc')
            ->get();

        $stats = [
            'total_du' => $echeances->whereNull('date_paiement')->sum('montant_du'),
            'total_paye' => $echeances->whereNotNull('date_paiement')->sum('montant_du'),
            'nombre_retard' => $echeances->filter(fn($e) => $e->estEnRetard())->count(),
            'nombre_payees' => $echeances->whereNotNull('date_paiement')->count()
        ];

        return view('echeances.rapport-contribuable', compact('contribuable', 'echeances', 'stats'));
    }

    /**
     * Générer un rappel pour échéances en retard
     */
    public function genererRappels()
    {
        $echeancesRetard = Echeance::enRetard()
            ->with(['contribuable', 'typeImpot'])
            ->get();

        // Ici, vous pouvez ajouter la logique d'envoi d'emails/SMS
        $rappels = [];
        foreach ($echeancesRetard as $echeance) {
            $calcul = $echeance->calculerPenalitesEtInterets();
            $rappels[] = [
                'contribuable' => $echeance->contribuable,
                'echeance' => $echeance,
                'calcul' => $calcul
            ];
        }

        return view('echeances.rappels', compact('rappels'));
    }
}
