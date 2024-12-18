<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'userName'=>'akevas',
            'role_id'=>1,
            'town_id'=>1,
            'phone_number'=>'657478382',
            'email'=>'contact@akevas.com',
            'password'=>bcrypt('password'),
        ]);
    }
}
