<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'userName'=>'amelia',
            'role_id'=>2,
            'phone_number'=>'676342432',
            'email'=>'amelia@gmail.com',
            'password'=>bcrypt('password'),
        ]);
    }
}
