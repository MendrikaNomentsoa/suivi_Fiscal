<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_Type';

    protected $fillable = [
        'libelle',
        'description',
    ]; 

    public function contribuables(){
        return $this->hasMany(Contribuable::class, 'id_Type');
    }
}
