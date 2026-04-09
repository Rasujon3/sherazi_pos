<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Admin',
            'email'         => 'admin@gmail.com',
            'phone'         => '01712345678',
            'user_type_id'  => 1, // admin
            'role'          => 'admin',
            'password'      => '123456', // Mutator will hash this password
            'remember_token'         => Str::random(60),
            'status'        => 'Active',
        ]);
    }
}
