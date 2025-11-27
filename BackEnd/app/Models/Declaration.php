<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Declaration extends Model
{
    // Nom exact de la table
    protected $table = 'declarations';

    // Clé primaire
    protected $primaryKey = 'id_declaration';

    // Colonnes autorisées pour le mass-assignment
    protected $fillable = [
        'id_type_impot',
        'id_contribuable',
        'montant',
        'date_declaration',
        'statut',
        'statut_paiement',   // ✅ AJOUTÉ
        'data'
    ];

    // Conversion automatique des types
    protected $casts = [
        'data' => 'array',
        'date_declaration' => 'datetime',
        'montant' => 'float',
        'statut_paiement' => 'string',   // ✅ CAST AUTOMATIQUE
    ];

    // Accessors personnalisés ajoutés automatiquement
    protected $appends = [
        'formatted_data',
        'paiement_label',    // ✅ AJOUTÉ
        'paiement_badge',    // ✅ AJOUTÉ
    ];

    // Clé primaire auto-incrémentée
    protected $keyType = 'int';
    public $incrementing = true;

    /**
     * 🔗 Type d'impôt lié
     */
    public function typeImpot(): BelongsTo
    {
        return $this->belongsTo(TypeImpot::class, 'id_type_impot', 'id_type_impot');
    }

    /**
     * 🔗 Contribuable lié
     */
    public function contribuable(): BelongsTo
    {
        return $this->belongsTo(Contribuable::class, 'id_contribuable', 'id_contribuable');
    }

    /**
     * 🔗 Échéance liée
     */
    public function echeance(): HasOne
    {
        return $this->hasOne(Echeance::class, 'id_declaration', 'id_declaration');
    }

    /**
     * 📝 Accessor pour formater les données JSON
     */
    public function getFormattedDataAttribute()
    {
        $data = $this->data ?? [];
        $formatted = [];

        foreach ($data as $key => $value) {
            if (is_numeric($value)) {
                $formatted[$key] = [
                    'value' => $value,
                    'formatted' => number_format((float)$value, 0, ',', ' ') . ' Ar'
                ];
            } else {
                $formatted[$key] = [
                    'value' => $value,
                    'formatted' => (string)$value
                ];
            }
        }

        return $formatted;
    }

    /**
     * 🎨 Accessor : Label du paiement
     */
    public function getPaiementLabelAttribute()
    {
        return match ($this->statut_paiement) {
            'paye' => 'Payé',
            'non_paye' => 'Non payé',
            default => 'Inconnu',
        };
    }

    /**
     * 🎨 Accessor : Style badge Tailwind selon paiement
     */
    public function getPaiementBadgeAttribute()
    {
        return match ($this->statut_paiement) {
            'paye' => 'bg-green-100 text-green-700 border border-green-200',
            'non_paye' => 'bg-red-100 text-red-700 border border-red-200',
            default => 'bg-gray-100 text-gray-600 border',
        };
    }

    /**
     * 🔍 Scope : filtrer par contribuable
     */
    public function scopeByContribuable($query, $idContribuable)
    {
        return $query->where('id_contribuable', $idContribuable);
    }

    /**
     * 🔍 Scope : filtrer par type d'impôt
     */
    public function scopeByTypeImpot($query, $idTypeImpot)
    {
        return $query->where('id_type_impot', $idTypeImpot);
    }

    /**
     * 🔍 Scope : filtrer par statut (validée / en_attente)
     */
    public function scopeByStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }
}
