<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Team;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Team::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'John Doe',
                'designation' => 'Manager',
                'image' => 'default-photo.png',
                'bio' => 'John is an experienced manager with over 10 years in the industry.',
            ]
        );
        Team::updateOrCreate(
            ['id' => 2],
            [
                'name' => 'Jane Smith',
                'designation' => 'Developer',
                'image' => 'default-photo.png',
                'bio' => 'Jane is a skilled developer with a passion for coding.',
            ]
        );
        Team::updateOrCreate(
            ['id' => 3],
            [
                'name' => 'Mike Johnson',
                'designation' => 'Designer',
                'image' => 'default-photo.png',
                'bio' => 'Mike is a creative designer with a keen eye for detail.',
            ]
        );
    }
}
