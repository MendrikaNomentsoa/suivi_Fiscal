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
        'data'
    ];

    // ✅ Conversion automatique des types
    protected $casts = [
        'data' => 'array', // Convertir automatiquement JSON <-> Array
        'date_declaration' => 'datetime',
        'montant' => 'float'
    ];

    // Pour inclure l'accessor formatted_data dans le JSON
    protected $appends = ['formatted_data'];

    // Définir le type de clé primaire comme integer
    protected $keyType = 'int';
    public $incrementing = true;

    /**
     * Relation vers le type d'impôt
     */
    public function typeImpot(): BelongsTo
    {
        return $this->belongsTo(TypeImpot::class, 'id_type_impot', 'id_type_impot');
    }

    /**
     * Relation vers le contribuable
     */
    public function contribuable(): BelongsTo
    {
        return $this->belongsTo(Contribuable::class, 'id_contribuable', 'id_contribuable');
    }

    /**
     * Relation vers l'échéance
     */
    public function echeance(): HasOne
    {
        return $this->hasOne(Echeance::class, 'id_declaration', 'id_declaration');
    }

    /**
     * Accessor pour obtenir les données formatées
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
     * Scope pour filtrer par contribuable
     */
    public function scopeByContribuable($query, $idContribuable)
    {
        return $query->where('id_contribuable', $idContribuable);
    }

    /**
     * Scope pour filtrer par type d'impôt
     */
    public function scopeByTypeImpot($query, $idTypeImpot)
    {
        return $query->where('id_type_impot', $idTypeImpot);
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeByStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }
}
