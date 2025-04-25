<?php

namespace Database\Seeders;

use App\Models\AttributeValue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttributeValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AttributeValue::create([
            'attribute_id' => 1,
            'value' => "rouge",
            "hex_color"=>"#FF0000"
        ]);

        AttributeValue::create([
            'attribute_id' => 1,
            'value' => "bleu",
            "hex_color"=>"#0000FF"
        ]);

        AttributeValue::create([
            'attribute_id' => 1,
            'value' => "vert",
            "hex_color"=>"#00FF00"
        ]);

        AttributeValue::create([
            'attribute_id' => 1,
            'value' => "jaune",
            "hex_color"=>"#FFFF00"
        ]);

        AttributeValue::create([
            'attribute_id' => 1,
            'value' => "noir",
            "hex_color"=>"#000000"
        ]);

        AttributeValue::create([
            'attribute_id' => 1,
            'value' => "blanc",
            "hex_color"=>"#FFFFFF"
        ]);

        AttributeValue::create([
            'attribute_id' => 1,
            'value' => "gris",
            "hex_color"=>"#808080"
        ]);

        AttributeValue::create([
            'attribute_id' => 1,
            'value' => "orange",
            "hex_color"=>"#FFA500"
        ]);

        AttributeValue::create([
            'attribute_id' => 1,
            'value' => "violet",
            "hex_color"=>"#800080"
        ]);

        AttributeValue::create([
            'attribute_id' => 1,
            'value' => "rose",
            "hex_color"=>"#FFC0CB"
        ]);

        AttributeValue::create([
            'attribute_id' => 2,
            'value' => "XXS",
        ]);

        AttributeValue::create([
            'attribute_id' => 2,
            'value' => "XS",
        ]);

        AttributeValue::create([
            'attribute_id' => 2,
            'value' => "S",
        ]);

        AttributeValue::create([
            'attribute_id' => 2,
            'value' => "M"
        ]);

        AttributeValue::create([
            'attribute_id' => 2,
            'value' => "L"
        ]);

        AttributeValue::create([
            'attribute_id' => 2,
            'value' => "XL"
        ]);

        AttributeValue::create([
            'attribute_id' => 2,
            'value' => "XXL"
        ]);

        AttributeValue::create([
            'attribute_id' => 2,
            'value' => "3XL"
        ]);

        AttributeValue::create([
            'attribute_id' => 3,
            'value' => "100g"
        ]);

        AttributeValue::create([
            'attribute_id' => 3,
            'value' => "250g"
        ]);

        AttributeValue::create([
            'attribute_id' => 3,
            'value' => "500g"
        ]);

        AttributeValue::create([
            'attribute_id' => 3,
            'value' => "1kg"
        ]);

        AttributeValue::create([
            'attribute_id' => 3,
            'value' => "2kg"
        ]);

        AttributeValue::create([
            'attribute_id' => 3,
            'value' => "5kg"
        ]);

        AttributeValue::create([
            'attribute_id' => 3,
            'value' => "10kg"
        ]);

        AttributeValue::create([
            'attribute_id' => 4,
            'value' => "38"
        ]);
        
    }
}
