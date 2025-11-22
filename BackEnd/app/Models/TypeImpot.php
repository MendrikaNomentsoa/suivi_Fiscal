<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeImpot extends Model
{
    // Nom exact de la table (par défaut Laravel mettrait type_impots)
    protected $table = 'type_impots';

    // Clé primaire
    protected $primaryKey = 'id_type_impot';

    // Pour indiquer que la clé primaire est BIGINT non auto-incrementable si besoin
    // protected $keyType = 'int'; // facultatif
    // public $incrementing = true; // true par défaut

    // Colonnes autorisées pour le mass-assignment
    protected $fillable = ['nom', 'periodicite'];

    /**
     * Relation avec les déclarations
     */
    public function declarations(): HasMany
    {
        return $this->hasMany(Declaration::class, 'id_type_impot', 'id_type_impot');
    }

    // App\Models\TypeImpot.php
    public function contribuables()
    {
        return $this->belongsToMany(Contribuable::class, 'contribuable_type_impot', 'id_type_impot', 'id_contribuable')
                    ->withTimestamps();
    }

}
