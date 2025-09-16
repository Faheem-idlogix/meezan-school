<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        User::create([
            'name' => 'Fahid',
            'email' => 'fahid@meezanschool.com',
            'password' => Hash::make('meezan#fahid@123'), // change later
        ]);

        User::create([
            'name' => 'Fahim',
            'email' => 'fahim@meezanschool.com',
            'password' => Hash::make('meezan#fahim@123'), // change later
        ]);
    }
}
