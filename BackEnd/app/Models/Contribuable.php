<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contribuable extends Model
{
    use HasFactory;

    // Nom de la clé primaire
    protected $primaryKey = 'id_contribuable';

    protected $table = 'contribuables';

    // Colonnes modifiables
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'mot_de_passe',
        'date_inscription',
    ];

    /*********************
     *   RELATIONS
     *********************/

    // Un contribuable a plusieurs déclarations
    public function declarations()
    {
        return $this->hasMany(Declaration::class, 'contribuable_id');
    }

    // Un contribuable peut avoir plusieurs échéances (si tu les lies au contribuable)
    public function echeances()
    {
        return $this->hasMany(Echeance::class, 'id_contribuable');
    }

    // Un contribuable peut avoir plusieurs litiges
    public function litiges()
    {
        return $this->hasMany(Litige::class, 'id_contribuable');
    }

    // Notifications via la table pivot NOTIFIER
    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'notifier', 'id_contribuable', 'id_notif')
                    ->withPivot('message')
                    ->withTimestamps();
    }
}
