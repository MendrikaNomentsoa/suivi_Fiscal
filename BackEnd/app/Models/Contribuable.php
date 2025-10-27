<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Contribuable extends Model
{

    use HasFactory;

    protected $primaryKey = 'id_Contribuable';
    protected $fillable = [
        'nom',
        'prenom',
        'mail',
        'telephone',
        'password',
        'date_inscription',
        'id_Type'
    ];

    public function categories(){
        return $this->belongsTo(Categorie::class, 'id_Type');
    }

    public function echeances(){
        return $this->hasMany(Echeance::class, 'id_Contribuable');
    }

    public function litiges(){
        return $this->hasMany(Litige::class, 'id_Contribuable');
    }

    public function notifications(){
        return $this->belongsToMany(Notification::class,'notifier', 'id_Contribuable', 'id_notif' )
                    ->withPivot('message')
                    ->withTimestamps();
    }


}
