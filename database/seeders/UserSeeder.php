<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('Secret123!@#'),
        ]);

        User::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'date_of_birth' => '1985-05-15',
            'gender' => 'female',
            'email' => 'jane.smith@example.com',
            'password' => Hash::make('Secret123!@#'),
        ]);

    }
}
