<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'category_name' => 'Beauté et santé',
            'parent_id' => null,
        ]);
        Category::create([
            'category_name' => 'Vêtements',
            'parent_id' => null,
        ]);
        Category::create([
            'category_name' => 'Bijoux',
            'parent_id' => null,
        ]);
        Category::create([
            'category_name' => 'Chaussures',
            'parent_id' => null,
        ]);
        Category::create([
            'category_name' => 'spécial fetes',
            'parent_id' => null,
        ]);
        Category::create([
            'category_name' => 'Parfums',
            'parent_id' => null,
        ]);
       
        
        
    }
}
