<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run()
    {
        Project::create([
            'name' => 'Project Alpha',
            'department' => 'IT',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'status' => 'active',
        ]);

        Project::create([
            'name' => 'Project Beta',
            'department' => 'HR',
            'start_date' => '2024-02-01',
            'end_date' => '2024-11-30',
            'status' => 'active',
        ]);

    }
}
