<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => "admin",
            'user_name' => 'admin',
            'email' => "admin@gmail.com",
            'password' => Hash::make('123456'),
            'active' => 1,
            'user_role' => 'admin',
        ]);
    }
}
