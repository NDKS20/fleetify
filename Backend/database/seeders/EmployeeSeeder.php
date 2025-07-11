<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Employee;
use App\Models\Division;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // Indonesian locale for more realistic names

        // Get all division IDs
        $divisionIds = Division::pluck('id')->toArray();

        $positions = [
            'Senior Developer',
            'Junior Developer',
            'Tech Lead',
            'Project Manager',
            'QA Engineer',
            'UI/UX Designer',
            'DevOps Engineer',
            'Product Manager',
            'Business Analyst',
            'Mobile Developer',
            'Frontend Developer',
            'Backend Developer',
            'Full Stack Developer',
        ];

        // Create 50 fake employees
        for ($i = 0; $i < 50; $i++) {
            Employee::create([
                'id' => Str::uuid(),
                'name' => $faker->name(),
                'phone' => $faker->phoneNumber(),
                'image' => $faker->imageUrl(400, 400, 'people', true, 'employee'), // Generates a fake image URL
                'division_id' => $faker->randomElement($divisionIds),
                'position' => $faker->randomElement($positions),
                'is_active' => $faker->boolean(85), // 85% chance of being active
                'deactivated_at' => null,
                'deactivated_by' => null,
            ]);
        }
    }
}
