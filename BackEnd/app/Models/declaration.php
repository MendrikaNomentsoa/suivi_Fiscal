<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Echeance extends Model
{
    use HasFactory;

    protected $table = 'echeances';
    protected $primaryKey = 'id_echeance';

    protected $fillable = [
        'id_declaration',
        'id_contribuable',
        'id_type_impot',
        'montant_du',
        'penalite',
        'interet',
        'date_limite',
        'date_paiement'
    ];

    // Relation vers Déclaration
    public function declaration()
    {
        return $this->belongsTo(Declaration::class, 'id_declaration', 'id');
    }

    // Relation vers Contribuable
    public function contribuable()
    {
        return $this->belongsTo(Contribuable::class, 'id_contribuable', 'id_contribuable');
    }

    // Relation vers Type d’impôt
    public function typeImpot()
    {
        return $this->belongsTo(TypeImpot::class, 'id_type_impot', 'id_type_impot');
    }
}
