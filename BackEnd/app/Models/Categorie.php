<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_Type';
    public $timestamps = false;

    protected $fillable = [
        'libelle',
        'design',
    ];

    public function contribuables(){
        return $this->hasMany(Contribuable::class, 'id_Type');
    }
}
