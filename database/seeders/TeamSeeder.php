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
                'image' => '1F5A3887.jpg',
                'bio' => 'John is an experienced manager with over 10 years in the industry.',
            ]
        );
        Team::updateOrCreate(
            ['id' => 2],
            [
                'name' => 'Jane Smith',
                'designation' => 'Developer',
                'image' => '1F5A4094.jpg',
                'bio' => 'Jane is a skilled developer with a passion for coding.',
            ]
        );
        Team::updateOrCreate(
            ['id' => 3],
            [
                'name' => 'Mike Johnson',
                'designation' => 'Designer',
                'image' => '1F5A4226.jpg',
                'bio' => 'Mike is a creative designer with a keen eye for detail.',
            ]
        );
        Team::updateOrCreate(
            ['id' => 4],
            [
                'name' => 'Emily Davis',
                'designation' => 'Marketing Specialist',
                'image' => '1F5A4011.jpg',
                'bio' => 'Emily is a marketing specialist with expertise in digital marketing.',
            ]
        );
        Team::updateOrCreate(
            ['id' => 5],
            [
                'name' => 'David Wilson',
                'designation' => 'Sales Executive',
                'image' => '1F5A3859.jpg',
                'bio' => 'David is a sales executive with a strong track record of success.',
            ]
            );
    }
}
