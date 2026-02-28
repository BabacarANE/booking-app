<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Chambre Standard',
                'slug'        => 'chambre-standard',
                'description' => 'Chambres confortables pour un séjour agréable',
                'icon'        => 'bed',
            ],
            [
                'name'        => 'Suite',
                'slug'        => 'suite',
                'description' => 'Suites luxueuses avec services premium',
                'icon'        => 'star',
            ],
            [
                'name'        => 'Salle de réunion',
                'slug'        => 'salle-reunion',
                'description' => 'Salles équipées pour vos réunions professionnelles',
                'icon'        => 'briefcase',
            ],
            [
                'name'        => 'Appartement',
                'slug'        => 'appartement',
                'description' => 'Appartements spacieux pour les longs séjours',
                'icon'        => 'home',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
