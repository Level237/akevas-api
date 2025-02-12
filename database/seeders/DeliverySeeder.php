<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
class DeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         User::create([
            'userName'=>'georges',
            'role_id'=>3,
            'phone_number'=>'656420363',
            'email'=>'georges@gmail.com',
            'password'=>bcrypt('password'),
        ]);
    }
}
