<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // Création des catégories principales
        $vetements = Category::create(['category_name' => 'Vêtements']);
        $bijoux = Category::create(['category_name' => 'Bijoux']);
        $chaussures = Category::create(['category_name' => 'Chaussures']);
        $parfums = Category::create(['category_name' => 'Parfums']);
        $meches = Category::create(['category_name' => 'Mèches']);
        $beaute = Category::create(['category_name' => 'Beauté']);
        $sport = Category::create(['category_name' => 'Sport']);
        $accessoires = Category::create(['category_name' => 'Accessoires']);
        $layette = Category::create(['category_name' => 'Spéciale Layette']);
        $mariage = Category::create(['category_name' => 'Spéciale Mariage']);

        // Création des sous-catégories Hommes, Femmes, Enfants une seule fois
        $hommes = Category::create(['category_name' => 'Hommes']);
        $femmes = Category::create(['category_name' => 'Femmes']);
        $enfants = Category::create(['category_name' => 'Enfants']);

        $traditionnel = Category::create(['category_name' => 'Tenues traditionnelles']);
        $pyjama = Category::create(['category_name' => 'Pyjamas']);
        $vetement_sport = Category::create(['category_name' => 'Vêtements de sport']);
        // Ajout des sous-catégories aux catégories Vêtements et Chaussures
        $vetements->children()->attach([$hommes->id, $femmes->id, $enfants->id]);
        $chaussures->children()->attach([$hommes->id, $femmes->id, $enfants->id]);

        // Sous-catégories Vêtements Hommes
        $hommes_categories = [
            'T-shirts et polos',
            'Chemises',
            'Pantalons (jeans, chinos, cargos, pagne)',
            'Costumes et smokings',
            'Shorts',
            'Sous-vêtements et Chaussettes',
            'Sweats & Hoodies',
            'Vestes',
            'Blousons',
            'Manteaux',
        ];
        $hommes->children()->attach($traditionnel->id);
        $vetements->children()->attach($traditionnel->id);
        $hommes->children()->attach($pyjama->id);
        $vetements->children()->attach($pyjama->id);
        foreach ($hommes_categories as $cat) {
            $sub_cat = Category::create(['category_name' => $cat]);
            $hommes->children()->attach($sub_cat->id);
            $vetements->children()->attach($sub_cat->id);
        }

        // Sous-catégories Vêtements Femmes
        $femmes_categories = [
            'Tops (T-shirts, débardeurs, chemisiers)',
            'Robes (soirée, décontractées, cérémonies)',
            'Jupes (longues, courtes, plissées)',
            'Pantalons et leggings',
            'Vêtements de grossesse',
            'Sous-vêtements et lingerie',
            'Vestes, blousons et manteaux',
        ];
        $femmes->children()->attach($vetement_sport->id);
        $vetements->children()->attach($vetement_sport->id);
        $femmes->children()->attach($traditionnel->id);
        $vetements->children()->attach($traditionnel->id);
        foreach ($femmes_categories as $cat) {
            $sub_cat = Category::create(['category_name' => $cat]);
            $femmes->children()->attach($sub_cat->id);
            $vetements->children()->attach($sub_cat->id);
        }

        // Sous-catégories Vêtements Enfants
        $enfants_categories = [
            'T-shirts et chemises',
            'Robes et jupes',
            'Shorts et pantalons',
            'Tenues scolaires',
            'Accessoires (casquettes, bonnets)'
        ];
        $enfants->children()->attach($vetement_sport->id);
        $vetements->children()->attach($vetement_sport->id);
        $enfants->children()->attach($pyjama->id);
        $vetements->children()->attach($pyjama->id);
        foreach ($enfants_categories as $cat) {
            $sub_cat = Category::create(['category_name' => $cat]);
            $enfants->children()->attach($sub_cat->id);
            $vetements->children()->attach($sub_cat->id);
        }

        // Sous-catégories Bijoux
        $bijoux_categories = [
            'Colliers (perles, pendentifs, chokers)',
            'Bracelets (joncs, chaînes, bracelets personnalisés)',
            'Boucles d’oreilles (clous, pendantes, créoles)',
            'Bagues (engagement, mode, alliance)',
            'Bijoux pour hommes (bracelets en cuir, chevalières)',
            'Montres (sport, luxe, connectées)',
            'Broches et pinces',
            'Bijoux ethniques',
            'Bijoux personnalisables (gravures)',
            'Coffrets de bijoux'
        ];
        foreach ($bijoux_categories as $cat) {
            $sub_cat = Category::create(['category_name' => $cat]);
            $bijoux->children()->attach($sub_cat->id);
        }

        // Sous-catégories Chaussures Hommes
        $hommes_chaussures_categories = [
            'Baskets et sneakers',
            'Chaussures de ville (cuir, mocassins)',
            'Sandales et tongs',
            'Bottes et bottines',
            'Chaussures pour occasions spéciales'
        ];
        foreach ($hommes_chaussures_categories as $cat) {
            $sub_cat = Category::create(['category_name' => $cat]);
            $hommes->children()->attach($sub_cat->id);
            $chaussures->children()->attach($sub_cat->id);
        }

        // Sous-catégories Chaussures Femmes
        $femmes_chaussures_categories = [
            'Escarpins',
            'Sandales (plates, compensées, à talons)',
            'Baskets et sneakers',
            'Bottes et cuissardes',
            'Mocassins et ballerines',
            'Chaussures de soirée',
            'Chaussures de mariage'
        ];
        foreach ($femmes_chaussures_categories as $cat) {
            $sub_cat = Category::create(['category_name' => $cat]);
            $femmes->children()->attach($sub_cat->id);
            $chaussures->children()->attach($sub_cat->id);
        }

        // Sous-catégories Chaussures Enfants
        $enfants_chaussures_categories = [
            'Baskets et sneakers',
            'Sandales',
            'Bottines',
            'Chaussures scolaires'
        ];
        foreach ($enfants_chaussures_categories as $cat) {
            $sub_cat = Category::create(['category_name' => $cat]);
            $enfants->children()->attach($sub_cat->id);
            $chaussures->children()->attach($sub_cat->id);
        }

        // Sous-catégories Parfums
        $parfums_categories = [
            'Parfums de luxe',
            'Eau de toilette',
            'Eau de parfum',
            'Brumes corporelles',
            'Parfums unisexes',
            'Coffrets cadeaux',
            'Parfums pour occasions spéciales',
            'Miniatures de parfum',
            'Parfums bio et naturels'
        ];
        foreach ($parfums_categories as $cat) {
            $sub_cat = Category::create(['category_name' => $cat]);
            $parfums->children()->attach($sub_cat->id);
        }

        // Sous-catégories Mèches
        $meches_categories = [
            'Tissages brésiliens',
            'Extensions naturelles',
            'Extensions synthétiques',
            'Perruques (courtes, longues, bouclées, lisses)',
            'Tresses africaines',
            'Franges et postiches',
            'Colorations de mèches',
            'Kits de soins pour mèches',
            'Accessoires pour cheveux (peignes, bonnets)'
        ];
        foreach ($meches_categories as $cat) {
            $sub_cat = Category::create(['category_name' => $cat]);
            $meches->children()->attach($sub_cat->id);
        }

        // Sous-catégories Beauté
        $soins_visage = Category::create(['category_name' => 'Soins du visage']);
        $maquillage = Category::create(['category_name' => 'Maquillage']);
        $soins_corps = Category::create(['category_name' => 'Soins du corps']);
        
        $beaute->children()->attach([$soins_visage->id, $maquillage->id, $soins_corps->id]);

        $soins_visage_categories = [
            'Nettoyants et exfoliants',
            'Masques et peelings',
            'Crèmes hydratantes',
            'Sérums et huiles',
            'Soins anti-âge',
            'Soins pour peaux spécifiques (acné, tâches)'
        ];
        foreach ($soins_visage_categories as $cat) {
            $sub_cat = Category::create(['category_name' => $cat]);
            $soins_visage->children()->attach($sub_cat->id);
        }

        $maquillage_categories = [
            'Teint (fond de teint, poudres, correcteurs)',
            'Lèvres (rouges à lèvres, gloss, baumes)',
            'Yeux (mascara, eye-liner, palettes)',
            'Ongles (vernis, accessoires)'
        ];
        foreach ($maquillage_categories as $cat) {
            $sub_cat = Category::create(['category_name' => $cat]);
            $maquillage->children()->attach($sub_cat->id);
        }

        $soins_corps_categories = [
            'Crèmes et laits hydratants',
            'Gommages corporels',
            'Huiles essentielles et de massage',
            'Produits éclaircissants'
        ];
        foreach ($soins_corps_categories as $cat) {
            $sub_cat = Category::create(['category_name' => $cat]);
            $soins_corps->children()->attach($sub_cat->id);
        }

        // Sous-catégories Sport
        $sport_categories = [
            'Chaussures de sport (course, fitness, randonnée)',
            'Équipements (ballons, haltères, tapis)',
            'Accessoires (bouteilles, serviettes)',
            'Articles de sport nautique',
            'Équipements de musculation'
        ];
        $sport->children()->attach($vetement_sport->id);
        foreach ($sport_categories as $cat) {
            $sub_cat = Category::create(['category_name' => $cat]);
            $sport->children()->attach($sub_cat->id);
        }

        // Sous-catégories Accessoires
        $accessoires_categories = [
            'Sacs (sacs à main, sacs à dos, pochettes)',
            'Portefeuilles',
            'Lunettes de soleil',
            'Ceintures',
            'Chapeaux (casquettes, bérets)',
            'Montres',
            'Bijoux fantaisie'
        ];
        foreach ($accessoires_categories as $cat) {
            $sub_cat = Category::create(['category_name' => $cat]);
            $accessoires->children()->attach($sub_cat->id);
        }

        // Sous-catégories Spéciale Layette
        $layette_categories = [
            'Vêtements pour nouveau-nés',
            'Chaussures pour nourrissons',
            'Couvertures et gigoteuses',
            'Bavoirs',
            'Accessoires (bonnets, chaussons)',
            'Jouets pour bébés',
            'Kits pour la maternité'
        ];
        foreach ($layette_categories as $cat) {
            $sub_cat = Category::create(['category_name' => $cat]);
            $layette->children()->attach($sub_cat->id);
        }

        // Sous-catégories Spéciale Mariage
        $mariage_categories = [
            'Robes de mariée (classiques, modernes, traditionnelles)',
            'Costumes de mariage',
            'Bijoux pour mariés',
            'Accessoires (voiles, gants, couronnes)',
            'Décorations de mariage (tables, voitures)',
            'Cadeaux pour invités (boîtes personnalisées, objets souvenir)',
            'Tenues pour demoiselles d’honneur et enfants'
        ];
        foreach ($mariage_categories as $cat) {
            $sub_cat = Category::create(['category_name' => $cat]);
            $mariage->children()->attach($sub_cat->id);
        }
    }
}
