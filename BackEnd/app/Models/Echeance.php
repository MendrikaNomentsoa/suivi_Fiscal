<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Echeance extends Model
{
    protected $table = 'echeances';
    protected $primaryKey = 'id_echeance';

    protected $fillable = [
        'id_contribuable',
        'id_declaration',
        'id_type_impot',
        'montant_du',
        'penalite',
        'interet',
        'date_limite',
        'date_paiement',
        'statut'
    ];

    protected $casts = [
        'date_limite' => 'datetime',
        'date_paiement' => 'datetime',
        'montant_du' => 'decimal:2',
        'penalite' => 'decimal:2',
        'interet' => 'decimal:2'
    ];

    // Relations
    public function contribuable(): BelongsTo
    {
        return $this->belongsTo(Contribuable::class, 'id_contribuable', 'id_contribuable');
    }

    public function declaration(): BelongsTo
    {
        return $this->belongsTo(Declaration::class, 'id_declaration', 'id_declaration');
    }

    public function typeImpot(): BelongsTo
    {
        return $this->belongsTo(TypeImpot::class, 'id_type_impot', 'id_type_impot');
    }

    /**
     * Calculer automatiquement les pénalités et intérêts
     */
    public function calculerPenalitesEtInterets()
    {
        $now = Carbon::now();

        // Si pas de retard, pas de pénalité
        if ($now->lt($this->date_limite) || $this->date_paiement) {
            return [
                'penalite' => 0,
                'interet' => 0,
                'total' => $this->montant_du
            ];
        }

        // Calcul du nombre de mois de retard
        $mois_retard = $now->diffInMonths($this->date_limite);
        if ($mois_retard < 1) {
            $mois_retard = 1; // Minimum 1 mois
        }

        // Pénalité fixe selon le type d'impôt
        $penalite = $this->id_type_impot == 1 ? 100000 : 20000;

        // Intérêt de retard : 1% par mois sur le montant dû
        $interet = ($this->montant_du * 0.01) * $mois_retard;

        // Total à payer
        $total = $this->montant_du + $penalite + $interet;

        return [
            'penalite' => round($penalite, 2),
            'interet' => round($interet, 2),
            'mois_retard' => $mois_retard,
            'total' => round($total, 2)
        ];
    }

    /**
     * Appliquer les pénalités et intérêts au modèle
     */
    public function appliquerPenalites()
    {
        $calcul = $this->calculerPenalitesEtInterets();

        $this->penalite = $calcul['penalite'];
        $this->interet = $calcul['interet'];
        $this->save();

        return $calcul;
    }

    /**
     * Vérifier si l'échéance est en retard
     */
    public function estEnRetard(): bool
    {
        if ($this->date_paiement) {
            return false; // Déjà payé
        }

        return Carbon::now()->gt($this->date_limite);
    }

    /**
     * Obtenir le montant total à payer (avec pénalités)
     */
    public function getMontantTotalAttribute(): float
    {
        $calcul = $this->calculerPenalitesEtInterets();
        return $calcul['total'];
    }

    /**
     * Obtenir le statut de l'échéance
     */
    public function getStatutEcheanceAttribute(): string
    {
        if ($this->date_paiement) {
            return 'payee';
        }

        if ($this->estEnRetard()) {
            return 'en_retard';
        }

        return 'en_attente';
    }

    /**
     * Scope pour les échéances en retard
     */
    public function scopeEnRetard($query)
    {
        return $query->whereNull('date_paiement')
                     ->where('date_limite', '<', Carbon::now());
    }

    /**
     * Scope pour les échéances payées
     */
    public function scopePayees($query)
    {
        return $query->whereNotNull('date_paiement');
    }

    /**
     * Scope pour les échéances à venir
     */
    public function scopeAVenir($query)
    {
        return $query->whereNull('date_paiement')
                     ->where('date_limite', '>=', Carbon::now());
    }
}
