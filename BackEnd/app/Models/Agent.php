<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $primaryKey = 'id_Agent';
    protected $fillable =[
        'nom',
        'prenom',
        'mail',
        'telephone',
        'role',
        'password',
    ];

    public function litiges(){
        return $this->hasMany(Litige::class, 'traiter', 'id_Agent', 'id_Litige');
    }

    public function notifications(){
        return $this->hasMany(Notification::class,'notifier', 'id_Agent', 'id_Notif')
                    ->withPivot('message')
                    ->withTimestamps();
    }
}
