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
            'category_name' => 'Vêtements',
        ]);
        Category::create([
            'category_name' => 'Bijoux',
        ]);
        Category::create([
            'category_name' => 'Chaussures',
        ]);
        Category::create([
            'category_name' => 'Parfums',
        ]);
        Category::create([
            'category_name' => 'Mèches',
        ]);
        Category::create([
            'category_name' => 'Beauté',
            'parent_id' => null,
        ]);
         Category::create([
            'category_name' => 'Sports',
            ]);
        Category::create([
            'category_name' => 'Accessoires',
        ]);
        Category::create([
            'category_name' => 'Spéciale Layette',
        ]);
        Category::create([
            'category_name' => 'Spéciale Mariage',
        ]);
        Category::create([
            'category_name' => 'Hommes',
        ]);
        Category::create([
            'category_name' => 'Femmes',
        ]);
        Category::create([
            'category_name' => 'Enfants',
        ]);
        Category::create([
            'category_name' => 'T-shirts et polos',
        ]);
        Category::create([
            'category_name' => 'Chemises',
        ]);
        Category::create([
            'category_name' => 'Pantalons',
        ]);
        Category::create([
            'category_name' => 'Costumes et smokings',
        ]);
        Category::create([
            'category_name' => 'Shorts',
        ]);
        Category::create([
            'category_name' => 'Sous-vêtements et Chaussettes',
        ]);
        Category::create([
            'category_name' => 'Sweats & Hoodies',
        ]);
        Category::create([
            'category_name' => 'Pyjamas',
        ]);
        Category::create([
            'category_name' => 'Vestes',
        ]);
        Category::create([
            'category_name' => 'Blousons',
        ]);
        Category::create([
            'category_name' => 'Manteaux',
        ]);
        Category::create([
            'category_name' => 'Tenues traditionnelles',
        ]);
        Category::create([
            'category_name' => 'Tops',
        ]);
        Category::create([
            'category_name' => 'Robes',
        ]);
        Category::create([
            'category_name' => 'Jupes',
        ]);
        Category::create([
            'category_name' => 'Pantalons et leggings',
        ]);
        Category::create([
            'category_name' => 'Vêtements de sport',
        ]);
        Category::create([
            'category_name' => 'Vêtements de grossesse',
        ]);
        Category::create([
            'category_name' => 'Sous-vêtements et lingerie',
        ]);
        Category::create([
            'category_name' => 'Vêtements de nuit',
        ]);
        Category::create([
            'category_name' => 'Vêtements de nuit',
        ]);
        Category::create([
            'category_name' => 'Vestes, blousons et manteaux',
        ]);
        Category::create([
            'category_name' => 'T-shirts et chemises',
        ]);
        Category::create([
            'category_name' => 'Robes et jupes',
        ]);
        Category::create([
            'category_name' => 'Shorts et pantalons',
        ]);
        Category::create([
            'category_name' => 'Tenues scolaires',
        ]);
        Category::create([
            'category_name' => 'Accessoires(casquettes, bonnets)',
        ]);
        Category::create([
            'category_name' => 'Colliers (perles, pendentifs, chokers)',
        ]);
        Category::create([
            'category_name' => 'Bracelets (joncs, chaînes, bracelets personnalisés)',
        ]);
        Category::create([
            'category_name' => 'Boucles d’oreilles (clous, pendantes, créoles)',
        ]);
        Category::create([
            'category_name' => 'Bagues (engagement, mode, alliance)',
        ]);
        Category::create([
            'category_name' => 'Bijoux pour hommes (bracelets en cuir, chevalières)',
        ]);
        Category::create([
            'category_name' => 'Montres (sport, luxe, connectées)',
        ]);
        Category::create([
            'category_name' => 'Broches et pinces',
        ]);
        Category::create([
            'category_name' => 'Bijoux ethniques',
        ]);
        Category::create([
            'category_name' => 'Bijoux personnalisables (gravures)',
        ]);
        Category::create([
            'category_name' => 'Coffrets de bijoux',
        ]);
        Category::create([
            'category_name' => 'Baskets et sneakers',
        ]);
        Category::create([
            'category_name' => 'Chaussures de ville (cuir, mocassins)',
        ]);
        Category::create([
            'category_name' => 'Sandales et tongs',
        ]);
        Category::create([
            'category_name' => 'Bottes et bottines',
        ]);
        Category::create([
            'category_name' => 'Chaussures pour occasions spéciales',
        ]);
        Category::create([
            'category_name' => 'Escarpins',
        ]);
        Category::create([
            'category_name' => 'Sandales (plates, compensées, à talons)',
        ]);
        Category::create([
            'category_name' => 'Bottes et cuissardes',
        ]);
        Category::create([
            'category_name' => 'Mocassins et ballerines',
        ]);
        Category::create([
            'category_name' => 'Chaussures de soirée',
        ]);
        Category::create([
            'category_name' => 'Chaussures de mariage',
        ]);
        Category::create([
            'category_name' => 'Sandales',
        ]);
        Category::create([
            'category_name' => 'Bottines',
        ]);
        Category::create([
            'category_name' => 'Chaussures scolaires',
        ]);

        Category::create([
            'category_name' => 'Parfums de luxe',
        ]);
        Category::create([
            'category_name' => 'Eau de toilette',
        ]);
        Category::create([
            'category_name' => 'Eau de parfum',
        ]);
        Category::create([
            'category_name' => 'Brumes corporelles',
        ]);
        Category::create([
            'category_name' => 'Parfums unisexes',
        ]);
        Category::create([
            'category_name' => 'Coffrets cadeaux',
        ]);
        Category::create([
            'category_name' => 'Parfums pour occasions spéciales',
        ]);
        Category::create([
            'category_name' => 'Miniatures de parfum',
        ]);
        Category::create([
            'category_name' => 'Parfums bio et naturels',
        ]);
        Category::create([
            'category_name' => 'Tissages brésiliens',
        ]);
        Category::create([
            'category_name' => 'Extensions naturelles',
        ]);
        Category::create([
            'category_name' => 'Extensions synthétiques',
        ]);
        Category::create([
            'category_name' => 'Perruques',
        ]);
        Category::create([
            'category_name' => 'Tresses africaines',
        ]);
        Category::create([
            'category_name' => 'Franges et postiches',
        ]);
        Category::create([
            'category_name' => 'Colorations de mèches',
        ]);
        Category::create([
            'category_name' => 'Kits de soins pour mèches',
        ]);
        Category::create([
            'category_name' => 'Accessoires pour cheveux (peignes, bonnets)',
        ]);
        Category::create([
            'category_name' => 'Nettoyants et exfoliants',
        ]);
        Category::create([
            'category_name' => 'Masques et peelings',
        ]);
        Category::create([
            'category_name' => 'Crèmes hydratantes',
        ]);
        Category::create([
            'category_name' => 'Sérums et huiles',
        ]);
        Category::create([
            'category_name' => 'Crèmes pour le visage',
        ]);
        Category::create([
            'category_name' => 'Soins anti-âge',
        ]);
        Category::create([
            'category_name' => 'Soins pour peaux spécifiques (acné, tâches)',
        ]);
        Category::create([
            'category_name' => 'Teint (fond de teint, poudres, correcteurs)',
        ]);
        Category::create([
            'category_name' => 'Lèvres (rouges à lèvres, gloss, baumes)',
        ]);
        Category::create([
            'category_name' => 'Yeux (mascara, eye-liner, palettes)',
        ]);
        Category::create([
            'category_name' => 'Ongles (vernis, accessoires)',
        ]);
        Category::create([
            'category_name' => 'Crèmes et laits hydratants',
        ]);
        Category::create([
            'category_name' => 'Gommages corporels',
        ]);
        Category::create([
            'category_name' => 'Huiles essentielles et de massage',
        ]);
        Category::create([
            'category_name' => 'Produits éclaircissants',
        ]);
        Category::create([
            'category_name' => 'Vêtements de sport',
        ]);
        Category::create([
            'category_name' => 'Chaussures de sport',
        ]);
        Category::create([
            'category_name' => 'Équipements',
        ]);
        Category::create([
            'category_name' => 'Accessoires pour sport',
        ]);
        Category::create([
            'category_name' => 'Articles de sport nautique',
        ]);
        Category::create([
            'category_name' => 'Équipements de musculation',
        ]);
        Category::create([
            'category_name' => 'Sacs',
        ]);
        Category::create([
            'category_name' => 'Portefeuilles',
        ]);
        Category::create([
            'category_name' => 'Lunettes de soleil',
        ]);
        Category::create([
            'category_name' => 'Ceintures',
        ]);
        Category::create([
            'category_name' => 'Chapeaux',
        ]);
        Category::create([
            'category_name' => 'Montres',
        ]);
        Category::create([
            'category_name' => 'Bijoux fantaisie',
        ]);
        Category::create([
            'category_name' => 'Vêtements pour nouveau-nés',
        ]);
        Category::create([
            'category_name' => 'Chaussures pour nourrissons',
        ]);
        Category::create([
            'category_name' => 'Couvertures et gigoteuses',
        ]);
        Category::create([
            'category_name' => 'Bavoirs',
        ]);
        Category::create([
            'category_name' => 'Accessoires (bonnets, chaussons)',
        ]);
        Category::create([
            'category_name' => 'Jouets pour bébés',
        ]);
        Category::create([
            'category_name' => 'Kits pour la maternité',
        ]);
        Category::create([
            'category_name' => 'Robes de mariée',
        ]);
        Category::create([
            'category_name' => 'Costumes de mariage',
        ]);
        Category::create([
            'category_name' => 'Bijoux pour mariés',
        ]);
        Category::create([
            'category_name' => 'Accessoires (voiles, gants, couronnes)',
        ]);
        Category::create([
            'category_name' => 'Décorations de mariage (tables, voitures)',
        ]);
        Category::create([
            'category_name' => 'Cadeaux pour invités (boîtes personnalisées, objets souvenir)',
        ]);
        Category::create([
            'category_name' => 'Tenues pour demoiselles d’honneur et enfants',
        ]);
        

        
    }
}
