<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeImpotSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('type_impots')->insert([
            [
                'nom' => 'IRSA',
                'periodicite' => 'mensuel'
            ],
            [
                'nom' => 'IS',
                'periodicite' => 'annuel'
            ]
        ]);
    }
}
