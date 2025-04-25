<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;
use App\Models\AttributeValueGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PointureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crée l'attribut "pointure"
        $pointure = Attribute::firstOrCreate([
            'attributes_name' => 'pointure'
        ]);

        // Crée les groupes d’âge
        $groupLabels = ['Bébé', 'Enfant', 'Adulte'];

        $groups = collect($groupLabels)->mapWithKeys(function ($label) use ($pointure) {
            $group = AttributeValueGroup::create([
                'attribute_id' => $pointure->id,
                'label' => $label,
            ]);
            return [$label => $group->id];
        });

        // Données de pointures par groupe
        $values = [
            'Bébé' => ['16', '17', '18'],
            'Enfant' => ['28', '30', '32', '34',"35","36","37"],
            'Adulte' => ['38','39', '40',"41", '42',"43", '44',"45"],
        ];

        foreach ($values as $groupLabel => $pointures) {
            foreach ($pointures as $val) {
                AttributeValue::create([
                    'attribute_id' => $pointure->id,
                    'attribute_value_group_id' => $groups[$groupLabel],
                    'value' => $val,
                ]);
            }
        }
    }
}
