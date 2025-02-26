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
            'value' => "rouge"
        ]);

        AttributeValue::create([
            'attribute_id' => 1,
            'value' => "bleu"
        ]);

        AttributeValue::create([
            'attribute_id' => 1,
            'value' => "vert"
        ]);

        AttributeValue::create([
            'attribute_id' => 1,
            'value' => "jaune"
        ]);

        AttributeValue::create([
            'attribute_id' => 1,
            'value' => "noir"
        ]);

        AttributeValue::create([
            'attribute_id' => 1,
            'value' => "blanc"
        ]);

        AttributeValue::create([
            'attribute_id' => 1,
            'value' => "gris"
        ]);

        AttributeValue::create([
            'attribute_id' => 1,
            'value' => "orange"
        ]);

        AttributeValue::create([
            'attribute_id' => 1,
            'value' => "violet"
        ]);

        AttributeValue::create([
            'attribute_id' => 1,
            'value' => "rose"
        ]);

        AttributeValue::create([
            'attribute_id' => 2,
            'value' => "XXS"
        ]);

        AttributeValue::create([
            'attribute_id' => 2,
            'value' => "XS"
        ]);

        AttributeValue::create([
            'attribute_id' => 2,
            'value' => "S"
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
    }
}
