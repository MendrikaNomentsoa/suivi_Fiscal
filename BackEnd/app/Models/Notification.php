<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_notif';

    protected $fillable = [
        'design',
        'statut',
        'date_envoi',
        'date_lecture',
    ];

    /**
     *  Relation : une notification peut être envoyée à plusieurs agents
     * (relation N ↔ N via la table pivot 'notifier')
     */
    public function agents()
    {
        return $this->belongsToMany(Agent::class, 'notifier', 'id_notif', 'id_Agent')
                    ->withPivot('message')
                    ->withTimestamps();
    }

    /**
     *  Relation : une notification peut être reçue par plusieurs contribuables
     * (relation N ↔ N via la table pivot 'notifier')
     */
    public function contribuables()
    {
        return $this->belongsToMany(Contribuable::class, 'notifier', 'id_notif', 'id_Contribuable')
                    ->withPivot('message')
                    ->withTimestamps();
    }
}
