<?php

namespace Database\Seeders;

use App\Models\UserType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserType::create([
            'name' => 'Admin',
            'role' => 'admin',
            'is_showing' => false,
        ]);

        UserType::create([
            'name' => 'User',
            'role' => 'user',
            'is_showing' => true,
        ]);

        UserType::create([
            'name' => 'Service Provider',
            'role' => 'service_provider',
            'is_showing' => true,
        ]);
    }
}
