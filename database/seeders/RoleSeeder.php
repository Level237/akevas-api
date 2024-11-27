<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'role_name'=>'admin'
        ]);
        Role::create([
            'role_name'=>'seller'
        ]);

        Role::create([
            'role_name'=>'customer'
        ]);
        Role::create([
            'role_name'=>'delivery'
        ]);
    }
}
