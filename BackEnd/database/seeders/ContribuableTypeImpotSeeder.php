<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContribuableTypeImpotSeeder extends Seeder
{
    public function run(): void
    {
        // Exemple : contribuables et types d'impôts
        // Contribuable 1 : IRSA et IS
        // Contribuable 2 : IRSA seulement
        DB::table('contribuable_type_impot')->insert([
            ['id_contribuable' => 1, 'id_type_impot' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_contribuable' => 1, 'id_type_impot' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_contribuable' => 2, 'id_type_impot' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
