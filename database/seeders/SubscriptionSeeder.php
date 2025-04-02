<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Subscription::create([
            'subscription_name' => 'Free',
            'subscription_price' => "0",
            'subscription_duration' => '0',
        ]);

        Subscription::create([
            'subscription_name' => 'Classic',
            'subscription_price' => "5000",
            'subscription_duration' => '30',
        ]);

        Subscription::create([
            'subscription_name' => 'Premium',
            'subscription_price' => "10000",
            'subscription_duration' => '30',
        ]);
        
    }
}
