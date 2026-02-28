<?php

namespace Database\Seeders;

use App\Models\Resource;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        $standard  = Category::where('slug', 'chambre-standard')->first();
        $suite     = Category::where('slug', 'suite')->first();
        $reunion   = Category::where('slug', 'salle-reunion')->first();
        $appart    = Category::where('slug', 'appartement')->first();

        $resources = [
            [
                'category_id'    => $standard->id,
                'name'           => 'Chambre Standard 101',
                'slug'           => 'chambre-standard-101',
                'description'    => 'Chambre confortable avec vue sur le jardin.',
                'price_per_night'=> 89.00,
                'capacity'       => 2,
                'amenities'      => ['WiFi', 'TV', 'Climatisation', 'Salle de bain privée'],
                'images'         => ['room101.jpg'],
                'location'       => 'Paris',
                'is_active'      => true,
            ],
            [
                'category_id'    => $standard->id,
                'name'           => 'Chambre Standard 102',
                'slug'           => 'chambre-standard-102',
                'description'    => 'Chambre lumineuse avec vue sur la rue.',
                'price_per_night'=> 79.00,
                'capacity'       => 2,
                'amenities'      => ['WiFi', 'TV', 'Bureau'],
                'images'         => ['room102.jpg'],
                'location'       => 'Paris',
                'is_active'      => true,
            ],
            [
                'category_id'    => $suite->id,
                'name'           => 'Suite Présidentielle',
                'slug'           => 'suite-presidentielle',
                'description'    => 'Suite luxueuse avec jacuzzi et vue panoramique.',
                'price_per_night'=> 350.00,
                'capacity'       => 4,
                'amenities'      => ['WiFi', 'TV 4K', 'Jacuzzi', 'Minibar', 'Climatisation', 'Service en chambre'],
                'images'         => ['suite-presidentielle.jpg'],
                'location'       => 'Paris',
                'is_active'      => true,
            ],
            [
                'category_id'    => $reunion->id,
                'name'           => 'Salle Einstein',
                'slug'           => 'salle-einstein',
                'description'    => 'Salle de réunion équipée pour 20 personnes.',
                'price_per_night'=> 200.00,
                'capacity'       => 20,
                'amenities'      => ['WiFi', 'Projecteur', 'Tableau blanc', 'Climatisation', 'Café/Thé'],
                'images'         => ['salle-einstein.jpg'],
                'location'       => 'Paris',
                'is_active'      => true,
            ],
            [
                'category_id'    => $appart->id,
                'name'           => 'Appartement Deluxe',
                'slug'           => 'appartement-deluxe',
                'description'    => 'Appartement 2 pièces idéal pour les longs séjours.',
                'price_per_night'=> 150.00,
                'capacity'       => 3,
                'amenities'      => ['WiFi', 'Cuisine équipée', 'Lave-linge', 'TV', 'Parking'],
                'images'         => ['appart-deluxe.jpg'],
                'location'       => 'Lyon',
                'is_active'      => true,
            ],
        ];

        foreach ($resources as $resource) {
            Resource::create($resource);
        }
    }
}
