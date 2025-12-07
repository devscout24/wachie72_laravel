<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\PropertyMultipleImage;

class PropertyImageSeeder extends Seeder
{
    /*
     * Run the database seeds.
     */
    public function run(): void
    {
        PropertyMultipleImage::updateOrCreate(
            ['id' => 1],
            [
                'property_id' => 1,
                'image'       => 'uploads/properties/2.jpg',
            ]
        );

        PropertyMultipleImage::updateOrCreate(
            ['id' => 2],
            [
                'property_id' => 1,
                'image'       => 'uploads/properties/1.jpg',
            ]
        );

        PropertyMultipleImage::updateOrCreate(
            ['id' => 3],
            [
                'property_id' => 2,
                'image'       => 'uploads/properties/2.jpg',
            ]
        );
        PropertyMultipleImage::updateOrCreate(
            ['id' => 4],
            [
                'property_id' => 2,
                'image'       => 'uploads/properties/1.jpg',
            ]
        );
    }
}


