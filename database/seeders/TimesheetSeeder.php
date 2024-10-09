<?php

namespace Database\Seeders;

use App\Models\Timesheet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimesheetSeeder extends Seeder
{
    public function run()
    {
        Timesheet::create([
            'user_id' => 1,
            'project_id' => 1,
            'task_name' => 'Development Task',
            'date' => '2024-10-10',
            'hours' => 5,
        ]);

        Timesheet::create([
            'user_id' => 2,
            'project_id' => 1,
            'task_name' => 'Testing Task',
            'date' => '2024-10-11',
            'hours' => 3,
        ]);


    }
}
