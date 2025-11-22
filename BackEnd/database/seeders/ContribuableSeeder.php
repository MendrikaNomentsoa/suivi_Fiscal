<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contribuable;
use Illuminate\Support\Facades\Hash;

class ContribuableSeeder extends Seeder
{
    public function run()
    {
        // On peut créer plusieurs contribuables pour tester
        $contribuables = [
            [
                'nif' => '123456789',
                'nom' => 'Rakoto',
                'prenom' => 'Jean',
                'email' => 'jean.rakoto@example.com',
                'telephone' => '0331234567',
                'password' => Hash::make('secret123'),
                'date_inscription' => now(),
            ],
            [
                'nif' => '987654321',
                'nom' => 'Andrianarisoa',
                'prenom' => 'Lala',
                'email' => 'lala.andrianarisoa@example.com',
                'telephone' => '0337654321',
                'password' => Hash::make('secret123'),
                'date_inscription' => now(),
            ],
        ];

        foreach ($contribuables as $c) {
            Contribuable::updateOrCreate(
                ['nif' => $c['nif']], // Évite les doublons
                $c
            );
        }
    }
}
