<?php

namespace Database\Seeders;

use App\Models\Description;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DescriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Description::create([
            'description_name' => 'Création de votre boutique',
        ]);

        Description::create([
            'description_name' => 'Ajout des produits',
        ]);

        Description::create([
            'description_name' => 'Gestion des commandes',
        ]);

        Description::create([
            'description_name' => 'Catalogue de produits',
        ]);
        
         Description::create([
            'description_name' => 'Visibilité de votre boutique sur le moteur de recherche akevas',
        ]);
        
         Description::create([
            'description_name' => 'Visibilité de votre boutique sur la page d\'accueil',
        ]);

        Description::create([
            'description_name' => 'Modal overwiew sur votre boutique',
        ]);

        Description::create([
            'description_name' => 'Support client',
        ]);
    }
}
