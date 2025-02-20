<?php

namespace Database\Seeders;

use App\Models\Gender;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
       
        // Création des genres
        $homme = Gender::create(['gender_name' => 'Homme',"gender_profile"=>"/genders/mens.jpg","gender_description"=>"Découvrez notre collection exclusive pour hommes - Du style pour tous les jours"]);
        $femme = Gender::create(['gender_name' => 'Femme',"gender_profile"=>"/genders/girl.jpg","gender_description"=>"Découvrez notre collection exclusive pour femmes - Du style pour tous les jours"]);
        $enfant = Gender::create(['gender_name' => 'Enfant',"gender_profile"=>"/genders/enfants.jpg","gender_description"=>"Découvrez notre collection exclusive pour enfants - Du style pour tous les jours"]);
         $categories = [
            ['name' => 'Vêtements', 'url' => 'vetements','category_profile'=>"/categories/profile/dress.jpg"],
            ['name' => 'Bijoux', 'url' => 'bijoux','category_profile'=>"/categories/profile/bijoux.jpg"],
            ['name' => 'Chaussures', 'url' => 'chaussures','category_profile'=>"/categories/profile/chaussures.jpg"],
            ['name' => 'Parfums', 'url' => 'parfums','category_profile'=>"/categories/profile/parfums.jpg"],
            ['name' => 'Mèches', 'url' => 'meches','category_profile'=>"/categories/profile/meches.jpg"],
            ['name' => 'Beauté', 'url' => 'beaute','category_profile'=>"/categories/profile/beaute.jpg"],
            ['name' => 'Sport', 'url' => 'sport','category_profile'=>"/categories/profile/sports.jpg"],
            ['name' => 'Accessoires', 'url' => 'accessoires','category_profile'=>"/categories/profile/accessoires.jpg"],
        ];


        foreach ($categories as $categoryData) {
            $category = Category::create([
                'category_name' => $categoryData['name'],
                'category_url' => Str::slug($categoryData['name']),
                'category_profile'=> $categoryData['category_profile'],
                'parent_id' => null // Pas de parent pour les catégories principales
            ]);
            $parentCategories[$categoryData['name']] = $category;
        }
        $parentCategories['Vêtements']->genders()->attach([$homme->id, $femme->id, $enfant->id]);
        $parentCategories['Bijoux']->genders()->attach([$homme->id, $femme->id, $enfant->id]);
        $parentCategories['Chaussures']->genders()->attach([$homme->id, $femme->id, $enfant->id]);
        $parentCategories['Parfums']->genders()->attach([$homme->id, $femme->id, $enfant->id]);
        $parentCategories['Mèches']->genders()->attach($femme->id);
        $parentCategories['Beauté']->genders()->attach($femme->id);
        $parentCategories['Sport']->genders()->attach([$homme->id, $femme->id, $enfant->id]);
        $parentCategories['Accessoires']->genders()->attach([$homme->id, $femme->id, $enfant->id]);
        // Sous-catégories pour Vêtements
        $vetements = $parentCategories['Vêtements'];
        $sousCategoriesHommeVetements = [
            
                ["name"=>'T-shirts et polos',"url"=>"t-shirts-et-polos-homme"],
                ["name"=>'Chemises',"url"=>"chemises-homme"],
                ["name"=>'Pantalons',"url"=>"pantalons-homme"],
                ["name"=>'Costumes et smokings',"url"=>"costumes-et-smokings-homme"],
                ["name"=>'Shorts',"url"=>"shorts-homme"],
                ["name"=>'Sous-vêtements et Chaussettes',"url"=>"sous-vetement-et-chaussettes-homme"],
                ["name"=>'Sweats & Hoodies',"url"=>"sweats-&-hoodies-homme"],
                ["name"=>'Pyjamas',"url"=>"pyjamas-homme"],
                ["name"=>'Vestes',"url"=>"vestes-homme"],
                ["name"=>'Blousons',"url"=>"blousons-homme"],
                ["name"=>'Manteaux',"url"=>"manteaux-homme"],
                ["name"=>'Tenues traditionnelles',"url"=>"tenues-traditionnelles-homme"],
        ];

        foreach ($sousCategoriesHommeVetements as $categoryData) {
            $category = Category::create([
                'category_name' => $categoryData['name'],
                'category_url' => $categoryData['url'],
                'parent_id' =>$vetements->id
            ]);
            $category->genders()->attach($homme->id);
        }

        $sousCategoriesFemmeVetements = [
            ["name" => 'Tops', "url" => "tops-femme"],
            ["name" => 'Robes', "url" => "robes-femme"],
            ["name" => 'Jupes', "url" => "jupes-femme"],
            ["name" => 'Pantalons', "url" => "pantalons-femme"],
            ["name" => 'Vêtements de sport', "url" => "vetements-de-sport-femme"],
            ["name" => 'Sous-vêtements', "url" => "sous-vetements-femme"],
            ["name" => 'Vestes', "url" => "vestes-femme"],
            ["name" => 'Manteaux', "url" => "manteaux-femme"],
            ["name" => 'Tenues traditionnelles', "url" => "tenues-traditionnelles-femme"],
        ];

        foreach ($sousCategoriesFemmeVetements as $categoryData) {
            $category=Category::create([
                'category_name' => $categoryData['name'],
                'category_url' => $categoryData['url'],
                'parent_id' => $vetements->id
            ]);
             $category->genders()->attach($femme->id);
        }

        $sousCategoriesEnfantVetements = [
            
                ["name"=>'T-shirts et polos',"url"=>"t-shirts-et-polos-enfant"],
                ["name"=>'Chemises',"url"=>"chemises-enfant"],
                ["name"=>'Pantalons',"url"=>"pantalons-enfant"],
                ["name"=>'Costumes et smokings',"url"=>"costumes-et-smokings-enfant"],
                ["name"=>'Shorts',"url"=>"shorts-enfant"],
                ["name"=>'Sous-vêtements et Chaussettes',"url"=>"sous-vetement-et-chaussettes-enfant"],
                ["name"=>'Sweats & Hoodies',"url"=>"sweats-&-hoodies-enfant"],
                ["name"=>'Pyjamas',"url"=>"pyjamas-enfant"],
                ["name"=>'Vestes',"url"=>"vestes-enfant"],
                ["name"=>'Blousons',"url"=>"blousons-enfant"],
                ["name"=>'Manteaux',"url"=>"manteaux-enfant"],
                ["name"=>'Tenues traditionnelles',"url"=>"tenues-traditionnelles-enfant"],
        ];

        foreach ($sousCategoriesEnfantVetements as $categoryData) {
            $category=Category::create([
                'category_name' => $categoryData['name'],
                'category_url' => $categoryData['url'],
                'parent_id' => $vetements->id
            ]);
             $category->genders()->attach($enfant->id);
        }
        // Sous-catégories pour Bijoux
        $bijoux = $parentCategories['Bijoux'];
        $sousCategoriesBijouxHommes = [
            ["name" => 'Colliers', "url" => "colliers-homme"],
            ["name" => 'Bracelets', "url" => "bracelets-homme"],
            ["name" => 'Broche et pinces', "url" => "broche-et-pinces-homme"],
            ["name" => 'Bagues', "url" => "bagues-homme"],
            ["name" => 'Bijoux ethniques', "url" => "bijoux-ethniques-homme"],
            ["name" => 'Bijoux personalisable', "url" => "bijoux-personalisable-homme"],
            ["name" => 'Coffrets de bijoux', "url" => "coffrets-de-bijoux-homme"],
        ];

        foreach ($sousCategoriesBijouxHommes as $categoryData) {
            $category=Category::create([
                'category_name' => $categoryData['name'],
                'category_url' => $categoryData['url'],
                'parent_id' => $bijoux->id
            ]);
             $category->genders()->attach($homme->id);
        }

        $sousCategoriesBijouxFemmes = [
            ["name" => 'Colliers', "url" => "colliers-femme"],
            ["name" => 'Bracelets', "url" => "bracelets-femme"],
            ["name" => 'Boucles', "url" => "boucles-femmes"],
            ["name" => 'Broche et pinces', "url" => "broche-et-pinces-femme"],
            ["name" => 'Bijoux ethniques', "url" => "bijoux-ethniques-femme"],
            ["name" => 'Bijoux personalisable', "url" => "bijoux-personalisable-femme"],
            ["name" => 'Coffrets de bijoux', "url" => "coffrets-de-bijoux-femme"],
        ];

        foreach ($sousCategoriesBijouxFemmes as $categoryData) {
            $category=Category::create([
                'category_name' => $categoryData['name'],
                'category_url' => $categoryData['url'],
                'parent_id' => $bijoux->id
            ]);
             $category->genders()->attach($femme->id);
        }

        $sousCategoriesBijouxEnfants = [
            ["name" => 'Colliers', "url" => "colliers-enfants"],
            ["name" => 'Bracelets', "url" => "bracelets-enfants"],
            ["name" => 'Boucles', "url" => "boucles-enfants"],
            ["name" => 'Broche et pinces', "url" => "broche-et-pinces-enfants"],
            ["name" => 'Bijoux ethniques', "url" => "bijoux-ethniques-enfants"],
            ["name" => 'Bijoux personalisable', "url" => "bijoux-personalisable-enfants"],
            ["name" => 'Coffrets de bijoux', "url" => "coffrets-de-bijoux-enfants"],
        ];

        foreach ($sousCategoriesBijouxEnfants as $categoryData) {
            $category=Category::create([
                'category_name' => $categoryData['name'],
                'category_url' => $categoryData['url'],
                'parent_id' => $bijoux->id
            ]);
             $category->genders()->attach($enfant->id);
        }

        $chaussures = $parentCategories['Chaussures'];

        // Sous-catégories pour Hommes
        $sousCategoriesChaussuresHommes = [
            ["name" => 'Baskets et sneakers', "url" => "baskets-et-sneakers-homme"],
            ["name" => 'Chaussures de ville (cuir, mocassins)', "url" => "chaussures-de-ville-homme"],
            ["name" => 'Sandales et tongs', "url" => "sandales-et-tongs-homme"],
            ["name" => 'Bottes et bottines', "url" => "bottes-et-bottines-homme"],
            ["name" => 'Chaussures pour occasions spéciales', "url" => "chaussures-occasions-speciales-homme"],
        ];


foreach ($sousCategoriesChaussuresHommes as $categoryData) {
    $category=Category::create([
        'category_name' => $categoryData['name'],
        'category_url' => $categoryData['url'],
        'parent_id' => $chaussures->id
    ]);
     $category->genders()->attach($homme->id);
}

// Sous-catégories pour Femmes
$sousCategoriesChaussuresFemmes = [
    ["name" => 'Escarpins', "url" => "escarpins-femme"],
    ["name" => 'Sandales (plates, compensées, à talons)', "url" => "sandales-femme"],
    ["name" => 'Baskets et sneakers', "url" => "baskets-et-sneakers-femme"],
    ["name" => 'Bottes et cuissardes', "url" => "bottes-et-cuissardes-femme"],
    ["name" => 'Mocassins et ballerines', "url" => "mocassins-et-ballerines-femme"],
    ["name" => 'Chaussures de soirée', "url" => "chaussures-de-soiree-femme"],
    ["name" => 'Chaussures de mariage', "url" => "chaussures-de-mariage-femme"],
];

foreach ($sousCategoriesChaussuresFemmes as $categoryData) {
    $category=Category::create([
        'category_name' => $categoryData['name'],
        'category_url' => $categoryData['url'],
        'parent_id' => $chaussures->id
    ]);
     $category->genders()->attach($femme->id);
}

// Sous-catégories pour Enfants
$sousCategoriesChaussuresEnfants = [
    ["name" => 'Baskets et sneakers', "url" => "baskets-et-sneakers-enfants"],
    ["name" => 'Sandales', "url" => "sandales-enfants"],
    ["name" => 'Bottines', "url" => "bottines-enfants"],
    ["name" => 'Chaussures scolaires', "url" => "chaussures-scolaires-enfants"],
];

foreach ($sousCategoriesChaussuresEnfants as $categoryData) {
    $category=Category::create([
        'category_name' => $categoryData['name'],
        'category_url' => $categoryData['url'],
        'parent_id' => $chaussures->id
    ]);
     $category->genders()->attach($enfant->id);
}

// Sous-catégories pour Parfums
$parfums = $parentCategories['Parfums'];

// Sous-catégories pour Hommes
$sousCategoriesParfumsHommes = [
    ["name" => 'Parfums de luxe', "url" => "parfums-de-luxe-homme"],
    ["name" => 'Eau de toilette', "url" => "eau-de-toilette-homme"],
    ["name" => 'Eau de parfum', "url" => "eau-de-parfum-homme"],
    ["name" => 'Brumes corporelles', "url" => "brumes-corporelles-homme"],
    ["name" => 'Parfums unisexes', "url" => "parfums-unisexes-homme"],
    ["name" => 'Coffrets cadeaux', "url" => "coffrets-cadeaux-homme"],
    ["name" => 'Parfums pour occasions spéciales', "url" => "parfums-occasions-speciales-homme"],
    ["name" => 'Miniatures de parfum', "url" => "miniatures-de-parfum-homme"],
    ["name" => 'Parfums bio et naturels', "url" => "parfums-bio-et-naturels-homme"],
];

foreach ($sousCategoriesParfumsHommes as $categoryData) {
    $category=Category::create([
        'category_name' => $categoryData['name'],
        'category_url' => $categoryData['url'],
        'parent_id' => $parfums->id
    ]);
     $category->genders()->attach($homme->id);
}

// Sous-catégories pour Femmes
$sousCategoriesParfumsFemmes = [
    ["name" => 'Parfums de luxe', "url" => "parfums-de-luxe-femme"],
    ["name" => 'Eau de toilette', "url" => "eau-de-toilette-femme"],
    ["name" => 'Eau de parfum', "url" => "eau-de-parfum-femme"],
    ["name" => 'Brumes corporelles', "url" => "brumes-corporelles-femme"],
    ["name" => 'Parfums unisexes', "url" => "parfums-unisexes-femme"],
    ["name" => 'Coffrets cadeaux', "url" => "coffrets-cadeaux-femme"],
    ["name" => 'Parfums pour occasions spéciales', "url" => "parfums-occasions-speciales-femme"],
    ["name" => 'Miniatures de parfum', "url" => "miniatures-de-parfum-femme"],
    ["name" => 'Parfums bio et naturels', "url" => "parfums-bio-et-naturels-femme"],
];

foreach ($sousCategoriesParfumsFemmes as $categoryData) {
    $category=Category::create([
        'category_name' => $categoryData['name'],
        'category_url' => $categoryData['url'],
        'parent_id' => $parfums->id
    ]);
     $category->genders()->attach($femme->id);
}

// Sous-catégories pour Enfants
$sousCategoriesParfumsEnfants = [
    ["name" => 'Parfums de luxe', "url" => "parfums-de-luxe-enfants"],
    ["name" => 'Eau de toilette', "url" => "eau-de-toilette-enfants"],
    ["name" => 'Eau de parfum', "url" => "eau-de-parfum-enfants"],
    ["name" => 'Brumes corporelles', "url" => "brumes-corporelles-enfants"],
    ["name" => 'Parfums unisexes', "url" => "parfums-unisexes-enfants"],
    ["name" => 'Coffrets cadeaux', "url" => "coffrets-cadeaux-enfants"],
    ["name" => 'Parfums pour occasions spéciales', "url" => "parfums-occasions-speciales-enfants"],
    ["name" => 'Miniatures de parfum', "url" => "miniatures-de-parfum-enfants"],
    ["name" => 'Parfums bio et naturels', "url" => "parfums-bio-et-naturels-enfants"],
];

foreach ($sousCategoriesParfumsEnfants as $categoryData) {
    $category=Category::create([
        'category_name' => $categoryData['name'],
        'category_url' => $categoryData['url'],
        'parent_id' => $parfums->id
    ]);

     $category->genders()->attach($enfant->id);
}

$meches = $parentCategories['Mèches'];

// Sous-catégories pour Femmes
$sousCategoriesMechesFemmes = [
    ["name" => 'Tissages brésiliens', "url" => "tissages-bresiliens-femme"],
    ["name" => 'Extensions naturelles', "url" => "extensions-naturelles-femme"],
    ["name" => 'Extensions synthétiques', "url" => "extensions-synthetiques-femme"],
    ["name" => 'Perruques (courtes, longues, bouclées, lisses)', "url" => "perruques-femme"],
    ["name" => 'Tresses africaines', "url" => "tresses-africaines-femme"],
    ["name" => 'Franges et postiches', "url" => "franges-et-postiches-femme"],
    ["name" => 'Colorations de mèches', "url" => "colorations-de-meches-femme"],
    ["name" => 'Kits de soins pour mèches', "url" => "kits-de-soins-pour-meches-femme"],
    ["name" => 'Accessoires pour cheveux (peignes, bonnets)', "url" => "accessoires-pour-cheveux-femme"],
];

foreach ($sousCategoriesMechesFemmes as $categoryData) {
    $category=Category::create([
        'category_name' => $categoryData['name'],
        'category_url' => $categoryData['url'],
        'parent_id' => $meches->id
    ]);
     $category->genders()->attach($femme->id);
}

// Catégorie parente : Beauté
$beauté = $parentCategories['Beauté'];

// Sous-catégories pour Soins du visage
$soinsVisage = Category::create([
    'category_name' => 'Soins du visage',
    'category_url' => 'soins-du-visage',
    'parent_id' => $beauté->id,
]);

// Enfants de Soins du visage
$sousCategoriesVisage = [
    ["name" => 'Nettoyants et exfoliants', "url" => "nettoyants-et-exfoliants"],
    ["name" => 'Masques et peelings', "url" => "masques-et-peelings"],
    ["name" => 'Crèmes hydratantes', "url" => "cremes-hydratantes"],
    ["name" => 'Sérums et huiles', "url" => "serums-et-huiles"],
    ["name" => 'Soins anti-âge', "url" => "soins-anti-age"],
    ["name" => 'Soins pour peaux spécifiques (acné, tâches)', "url" => "soins-peaux-specifiques"],
];

foreach ($sousCategoriesVisage as $categoryData) {
    $category=Category::create([
        'category_name' => $categoryData['name'],
        'category_url' => $categoryData['url'],
        'parent_id' => $soinsVisage->id
    ]);
    
}

// Sous-catégories pour Maquillage
$maquillage = Category::create([
    'category_name' => 'Maquillage',
    'category_url' => 'maquillage',
    'parent_id' => $beauté->id,
]);

// Enfants de Maquillage
$sousCategoriesMaquillage = [
    ["name" => 'Teint (fond de teint, poudres, correcteurs)', "url" => "teint"],
    ["name" => 'Lèvres (rouges à lèvres, gloss, baumes)', "url" => "levres"],
    ["name" => 'Yeux (mascara, eye-liner, palettes)', "url" => "yeux"],
    ["name" => 'Ongles (vernis, accessoires)', "url" => "ongles"],
];

foreach ($sousCategoriesMaquillage as $categoryData) {
    Category::create([
        'category_name' => $categoryData['name'],
        'category_url' => $categoryData['url'],
        'parent_id' => $maquillage->id
    ]);
}

// Sous-catégories pour Soins du corps
$soinsCorps = Category::create([
    'category_name' => 'Soins du corps',
    'category_url' => 'soins-du-corps',
    'parent_id' => $beauté->id,
]);

// Enfants de Soins du corps
$sousCategoriesCorps = [
    ["name" => 'Crèmes et laits hydratants', "url" => "cremes-et-laits-hydratants"],
    ["name" => 'Gommages corporels', "url" => "gommages-corporels"],
    ["name" => 'Huiles essentielles et de massage', "url" => "huiles-essentielles"],
    ["name" => 'Produits éclaircissants', "url" => "produits-eclaircissants"],
];

foreach ($sousCategoriesCorps as $categoryData) {
    Category::create([
        'category_name' => $categoryData['name'],
        'category_url' => $categoryData['url'],
        'parent_id' => $soinsCorps->id
    ]);
}

// Sous-catégories pour Parfums
$sport = $parentCategories['Sport'];

// Sous-catégories pour Hommes
$sousCategoriesSportHommes = [
    ["name" => 'Vêtements de sport', "url" => "vetements-de-sport-homme"],
    ["name" => 'Chaussures de sport', "url" => "chaussure-de-sport-homme"],
    ["name" => 'Équipements (ballons, haltères, tapis)', "url" => "equipement-de-sport-homme"],
    ["name" => 'Accessoires (bouteilles, serviettes)', "url" => "accessoire-de-sport-homme"],
    ["name" => 'Équipements de musculation', "url" => "equipement-de-musculation-homme"],
];

foreach ($sousCategoriesSportHommes as $categoryData) {
    $category=Category::create([
        'category_name' => $categoryData['name'],
        'category_url' => $categoryData['url'],
        'parent_id' => $sport->id
    ]);
     $category->genders()->attach($homme->id);
}

// Sous-catégories pour Hommes
$sousCategoriesSportFemmes = [
    ["name" => 'Vêtements de sport', "url" => "vetements-de-sport-femme"],
    ["name" => 'Chaussures de sport', "url" => "chaussure-de-sport-femme"],
    ["name" => 'Équipements (ballons, haltères, tapis)', "url" => "equipement-de-sport-femme"],
    ["name" => 'Accessoires (bouteilles, serviettes)', "url" => "accessoire-de-sport-femme"],
    ["name" => 'Équipements de musculation', "url" => "equipement-de-musculation-femme"],
];

foreach ($sousCategoriesSportFemmes as $categoryData) {
    $category=Category::create([
        'category_name' => $categoryData['name'],
        'category_url' => $categoryData['url'],
        'parent_id' => $sport->id
    ]);
     $category->genders()->attach($femme->id);
}
      $sousCategoriesSportEnfant = [
    ["name" => 'Vêtements de sport', "url" => "vetements-de-sport-enfant"],
    ["name" => 'Chaussures de sport', "url" => "chaussure-de-sport-enfant"],
    ["name" => 'Équipements (ballons, haltères, tapis)', "url" => "equipement-de-sport-enfant"],
    ["name" => 'Accessoires (bouteilles, serviettes)', "url" => "accessoire-de-sport-enfant"],
    ["name" => 'Équipements de musculation', "url" => "equipement-de-musculation-enfant"],
];

foreach ($sousCategoriesSportEnfant as $categoryData) {
    $category=Category::create([
        'category_name' => $categoryData['name'],
        'category_url' => $categoryData['url'],
        'parent_id' => $sport->id
    ]);
     $category->genders()->attach($enfant->id);
}      

$accessoire = $parentCategories['Accessoires'];

 $sousCategoriesAccessoireHomme = [
    ["name" => 'Sacs (sacs à main, sacs à dos, pochettes)', "url" => "sacs-homme"],
    ["name" => 'Portefeuilles', "url" => "portefeuilles-homme"],
    ["name" => 'Lunettes de soleil', "url" => "lunettes-de-soleil-homme"],
    ["name" => 'Ceintures', "url" => "ceinture-homme"],
    ["name" => 'Chapeaux (casquettes, bérets)', "url" => "chapeau-homme"],
     ["name" => 'Montres', "url" => "montre-homme"],
      ["name" => 'Bijoux fantaisie', "url" => "bijoux-fantaisie-homme"],
];

foreach ($sousCategoriesAccessoireHomme as $categoryData) {
    $category=Category::create([
        'category_name' => $categoryData['name'],
        'category_url' => $categoryData['url'],
        'parent_id' => $accessoire->id
    ]);
     $category->genders()->attach($homme->id);
} 

$sousCategoriesAccessoireFemmes = [
    ["name" => 'Sacs (sacs à main, sacs à dos, pochettes)', "url" => "sacs-femme"],
    ["name" => 'Portefeuilles', "url" => "portefeuilles-femme"],
    ["name" => 'Lunettes de soleil', "url" => "lunettes-de-soleil-femme"],
    ["name" => 'Ceintures', "url" => "ceinture-femme"],
    ["name" => 'Chapeaux (casquettes, bérets)', "url" => "chapeau-femme"],
     ["name" => 'Montres', "url" => "montre-femme"],
      ["name" => 'Bijoux fantaisie', "url" => "bijoux-fantaisie-femme"],
];

foreach ($sousCategoriesAccessoireFemmes as $categoryData) {
    $category=Category::create([
        'category_name' => $categoryData['name'],
        'category_url' => $categoryData['url'],
        'parent_id' => $accessoire->id
    ]);
     $category->genders()->attach($femme->id);
} 

$sousCategoriesAccessoireEnfants = [
    ["name" => 'Sacs (sacs à main, sacs à dos, pochettes)', "url" => "sacs-enfant"],
    ["name" => 'Portefeuilles', "url" => "portefeuilles-enfant"],
    ["name" => 'Lunettes de soleil', "url" => "lunettes-de-soleil-enfant"],
    ["name" => 'Ceintures', "url" => "ceinture-enfant"],
    ["name" => 'Chapeaux (casquettes, bérets)', "url" => "chapeau-enfant"],
     ["name" => 'Montres', "url" => "montre-enfant"],
      ["name" => 'Bijoux fantaisie', "url" => "bijoux-fantaisie-enfant"],
];

foreach ($sousCategoriesAccessoireEnfants as $categoryData) {
    $category=Category::create([
        'category_name' => $categoryData['name'],
        'category_url' => $categoryData['url'],
        'parent_id' => $accessoire->id
    ]);
     $category->genders()->attach($enfant->id);
} 


    }
}
