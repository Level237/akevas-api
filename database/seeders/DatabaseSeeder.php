<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $path = 'database/sql_files/oauth_client.sql';
        DB::unprepared(file_get_contents($path));
        $this->command->info('client table seeded!');
        $this->call([
            TownSeeder::class,
            QuarterSeeder::class,
            RoleSeeder::class,
            AdminSeeder::class,
            DeliverySeeder::class,
            AttributeSeeder::class,
            AttributeValueSeeder::class,
            CategorySeeder::class,
            ShopSeeder::class,
            ProductSeeder::class,
            SubscriptionSeeder::class
        ]);
    }
}
