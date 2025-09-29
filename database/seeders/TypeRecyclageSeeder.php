<?php

namespace Database\Seeders;

use App\Models\TypeRecyclage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeRecyclageSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $types = [
            [
                'nom' => 'Plastique',
                'description' => 'Bouteilles, emballages, sacs plastiques et autres déchets en plastique recyclable',
                'couleur' => '#28a745',
                'icone' => 'ti-package',
                'actif' => true
            ],
            [
                'nom' => 'Papier',
                'description' => 'Journaux, magazines, cartons, papiers de bureau et emballages en papier',
                'couleur' => '#ffc107',
                'icone' => 'ti-files',
                'actif' => true
            ],
            [
                'nom' => 'Verre',
                'description' => 'Bouteilles, bocaux et autres contenants en verre',
                'couleur' => '#17a2b8',
                'icone' => 'ti-cup',
                'actif' => true
            ],
            [
                'nom' => 'Métal',
                'description' => 'Canettes, boîtes de conserve, emballages métalliques',
                'couleur' => '#6c757d',
                'icone' => 'ti-settings',
                'actif' => true
            ],
            [
                'nom' => 'Électronique',
                'description' => 'Téléphones, ordinateurs, appareils électroniques usagés',
                'couleur' => '#dc3545',
                'icone' => 'ti-mobile',
                'actif' => true
            ],
            [
                'nom' => 'Textile',
                'description' => 'Vêtements, chaussures, textiles usagés en bon état',
                'couleur' => '#e83e8c',
                'icone' => 'ti-shirt',
                'actif' => true
            ]
        ];

        foreach ($types as $type) {
            TypeRecyclage::create($type);
        }
    }
}
