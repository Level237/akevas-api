<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DescriptionSubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            DB::table('description_subscription')->insert([
                'description_id' => 1,
                'subscription_id' => 1,
            ]);
            DB::table('description_subscription')->insert([
                'description_id' => 2,
                'subscription_id' => 1,
            ]);

            DB::table('description_subscription')->insert([
                'description_id' => 4,
                'subscription_id' => 1,
            ]);

            DB::table('description_subscription')->insert([
                'description_id' => 5,
                'subscription_id' => 2,
            ]);
            DB::table('description_subscription')->insert([
                'description_id' =>6,
                'subscription_id' => 2,
            ]);

            DB::table('description_subscription')->insert([
                'description_id' => 7,
                'subscription_id' => 2,
            ]);

            DB::table('description_subscription')->insert([
                'description_id' => 8,
                'subscription_id' => 3,
            ]);

            DB::table('description_subscription')->insert([
                'description_id' => 9,
                'subscription_id' => 3,
            ]);

            DB::table('description_subscription')->insert([
                'description_id' => 10,
                'subscription_id' => 3,
            ]);

            DB::table('description_subscription')->insert([
                'description_id' => 11,
                'subscription_id' => 3,
            ]);
            
            
    }
}
