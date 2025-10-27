<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Echeance extends Model
{
    protected $primaryKey = 'id_Echeance';
    protected $fillable = [
        'montant',
        'date_limite',
        'penalite',
        'id_Contribuable',
    ];

    public function contribuables(){
        return $this->belongsTo(Contribuable::class, 'id_Contribuable');
    }
}
