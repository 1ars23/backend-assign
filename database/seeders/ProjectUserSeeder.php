<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectUserSeeder extends Seeder
{
    public function run()
    {
        DB::table('project_user')->insert([
            'project_id' => 1,
            'user_id' => 1,
        ]);

        DB::table('project_user')->insert([
            'project_id' => 1,
            'user_id' => 2,
        ]);

    }
}
