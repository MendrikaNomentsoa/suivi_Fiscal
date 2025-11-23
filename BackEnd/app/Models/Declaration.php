<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'statut'
    ];

    // Définir le type de clé primaire comme integer (optionnel si auto-increment)
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
}
