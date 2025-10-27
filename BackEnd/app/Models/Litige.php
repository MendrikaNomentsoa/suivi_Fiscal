<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Litige extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_Litige';

    protected $fillable = [
        'sujet',
        'description',
        'date_ouverture',
        'statut',
        'id_Contribuable',
    ];

    /**
     * 🔗 Relation : un litige appartient à un contribuable
     */
    public function contribuable()
    {
        return $this->belongsTo(Contribuable::class, 'id_Contribuable');
    }

    /**
     * 🔗 Relation : un litige peut être traité par plusieurs agents
     * (relation N ↔ N via la table pivot 'traiter')
     */
    public function agents()
    {
        return $this->belongsToMany(Agent::class, 'traiter', 'id_Litige', 'id_Agent')
                    ->withTimestamps();
    }
}
