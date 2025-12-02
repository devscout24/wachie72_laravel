<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        Property::updateOrCreate(
            ['id' => 1],
            [
                'user_id'       => 1,
                'main_image'    => json_encode(['default-image.png']),
                'multiple_image'=> json_encode(['default-image.png', 'default-image2.png']),
                'title'         => 'Luxury Beachside Villa',
                'location'      => 'Cox\'s Bazar, Bangladesh',
                'rating'        => null,
                'total_reviews' => 0,
                'price'         => 350,
                'cleaning_fee'   => 50,
                'bedrooms'      => 4,
                'bathrooms'     => 3,
                'max_guests'    => 8,
                'amenity_id'    => null,
                'description'   => 'A beautiful luxury villa with sea view and private pool.',
                'status'        => 1,
            ]
        );

        Property::updateOrCreate(
            ['id' => 2],
            [
                'user_id'       => 1,
                'main_image'    => json_encode(['default-image2.png']),
                'multiple_image'=> json_encode(['default-image.png', 'default-image2.png']),
                'title'         => 'Modern City Apartment',
                'location'      => 'Dhaka, Bangladesh',
                'rating'        => null,
                'total_reviews' => 0,
                'price'         => 120,
                'cleaning_fee'   => 20,
                'bedrooms'      => 2,
                'bathrooms'     => 2,
                'max_guests'    => 4,
                'amenity_id'    => null,
                'description'   => 'Comfortable apartment with modern facilities in the heart of the city.',
                'status'        => 1,
            ]
        );

        Property::updateOrCreate(
            ['id' => 3],
            [
                'user_id'       => 1,
                'main_image'    => json_encode(['default-image3.png']),
                'multiple_image'=> json_encode(['default-image3.png', 'default-image4.png']),
                'title'         => 'Cozy Cottage in the Woods',
                'location'      => 'Chittagong, Bangladesh',
                'rating'        => null,
                'total_reviews' => 0,
                'price'         => 80,
                'cleaning_fee'   => 15,
                'bedrooms'      => 1,
                'bathrooms'     => 1,
                'max_guests'    => 2,
                'amenity_id'    => null,
                'description'   => 'A cozy cottage nestled in the woods, perfect for nature lovers.',
                'status'        => 1,
            ]
        );
    }
}
