<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable; // si tu veux utiliser l'authentification
use Laravel\Sanctum\HasApiTokens;

class Contribuable extends Model
{
    use HasApiTokens, HasFactory;

    // Nom de la clé primaire
    protected $primaryKey = 'id_contribuable';

    protected $table = 'contribuables';

    // Colonnes modifiables
    protected $fillable = [
        'nif',               // <- ajouté pour ton workflow
        'nom',
        'prenom',
        'email',
        'telephone',
        'password',
        'date_inscription'
    ];

    // Si tu veux cacher certains champs dans les réponses JSON
    protected $hidden = [
        'password',
    ];

    /*********************
     *   RELATIONS
     *********************/

    // App\Models\Contribuable.php
    public function typeImpots()
    {
        return $this->belongsToMany(TypeImpot::class, 'contribuable_type_impot', 'id_contribuable', 'id_type_impot')
                    ->withTimestamps();
    }


    // Un contribuable a plusieurs déclarations
    public function declarations()
    {
        return $this->hasMany(Declaration::class, 'id_contribuable');
    }

    // Un contribuable peut avoir plusieurs échéances
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
        return $this->belongsToMany(
            Notification::class,
            'notifier',
            'id_contribuable',
            'id_notif'
        )->withPivot('message')
         ->withTimestamps();
    }
}
