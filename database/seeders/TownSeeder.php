<?php

namespace Database\Seeders;

use App\Models\Town;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TownSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Town::create([
            'town_name'=>"Douala",
            'code'=>"DLA"
        ]);

        Town::create([
            'town_name'=>"Yaoundé",
            'code'=>"YND"
        ]);
    }
}
