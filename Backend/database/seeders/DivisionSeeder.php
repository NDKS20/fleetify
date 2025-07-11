<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\Division;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = [
            'Mobile Apps',
            'QA',
            'Full Stack',
            'Backend',
            'Frontend',
            'UI/UX Designer',
        ];

        foreach ($divisions as $division) {
            Division::create([
                'id' => Str::uuid(),
                'name' => $division,
            ]);
        }
    }
}
