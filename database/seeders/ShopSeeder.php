 <?php


use App\Models\Shop;
use App\Models\User;
use App\Models\Image;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Services\Shop\generateShopNameService;

class ShopSeeder extends Seeder
{
    public function run()
    {
        $shops = [
            [
                'user' => [
                    'firstName' => 'Jean',
                    'lastName' => 'Dupont',
                    'email' => 'jean.dupont@example.com',
                    'phone_number' => '677123456',
                    'birthDate' => '1990-01-15',
                    'isWholesaler' => true,
                    'nationality' => 'Camerounaise',
                    'password' => 'password123'
                ],
                'shop' => [
                    'shop_name' => 'Élégance Mode',
                    'shop_description' => 'Boutique de vêtements haut de gamme',
                    'town_id' => 1,
                    "shop_profile"=>"shop/profile/shop1.jpeg",
                    'quarter_id' => 1,
                    'product_type' => "0",
                    'shop_gender' => "4",
                    'categories' => [1, 3, 7]
                ]
            ],
            [
                'user' => [
                    'firstName' => 'Marie',
                    'lastName' => 'Kouam',
                    'email' => 'marie.kouam@example.com',
                    'phone_number' => '677234567',
                    'birthDate' => '1992-03-20',
                   
                    'isWholesaler' => false,
                    'nationality' => 'Camerounaise',
                    'password' => 'password123'
                ],
                'shop' => [
                    'shop_name' => 'Beauty Corner',
                    'shop_description' => 'Produits de beauté et cosmétiques',
                    'town_id' => 2,
                    'quarter_id' => 32,
                    'product_type' => "1",
                     "shop_profile"=>"shop/profile/shop2.jpg",
                    'shop_gender' => "2",
                    'categories' => [5, 6]
                ]
            ],
            [
                'user' => [
                    'firstName' => 'Paul',
                    'lastName' => 'Biya',
                    'email' => 'paul.biya@example.com', 
                    'phone_number' => '677345678',
                    'birthDate' => '1988-05-12',
                    'isWholesaler' => true,
                    'nationality' => 'Camerounaise',
                    'password' => 'password123'
                ],
                'shop' => [
                    'shop_name' => 'Sport Elite',
                    'shop_description' => 'Équipements et vêtements de sport',
                    'town_id' => 1,
                    'quarter_id' => 3,
                    "shop_profile"=>"shop/profile/shop3.jpg",
                    'product_type' => 'Sport',
                    'shop_gender' => "4",
                    'categories' => [7]
                ]
            ],
            [
                'user' => [
                    'firstName' => 'Sophie',
                    'lastName' => 'Mbarga',
                    'email' => 'sophie.mbarga@example.com',
                    'phone_number' => '677456789',
                    'birthDate' => '1995-07-25',
                    'isWholesaler' => false,
                    'nationality' => 'Camerounaise', 
                    'password' => 'password123'
                ],
                'shop' => [
                    'shop_name' => 'Bijoux Précieux',
                    'shop_description' => 'Bijouterie fine et accessoires',
                    'town_id' => 2,
                    'quarter_id' => 35,
                    "shop_profile"=>"shop/profile/shop4.jpg",
                    'product_type' => "1",
                    'shop_gender' => "2",
                    'categories' => [2, 8]
                ]
            ],
            [
                'user' => [
                    'firstName' => 'Pierre',
                    'lastName' => 'Kamdem',
                    'email' => 'pierre.kamdem@example.com',
                    'phone_number' => '677567890',
                    'birthDate' => '1991-09-30',
                    'isWholesaler' => true,
                    'nationality' => 'Camerounaise',
                    'password' => 'password123'
                ],
                'shop' => [
                    'shop_name' => 'Chaussures Plus',
                    'shop_description' => 'Chaussures pour toute la famille',
                    'town_id' => 1,
                    'quarter_id' => 5,
                    "shop_profile"=>"shop/profile/shop5.jpg",
                    'product_type' => "1",
                    'shop_gender' => "4",
                    'categories' => [3]
                ]
            ],
            [
                'user' => [
                    'firstName' => 'Claire',
                    'lastName' => 'Fotso',
                    'email' => 'claire.fotso@example.com',
                    'phone_number' => '677678901',
                    'birthDate' => '1993-11-15',
                    'isWholesaler' => false,
                    'nationality' => 'Camerounaise',
                    'password' => 'password123'
                ],
                'shop' => [
                    'shop_name' => 'Parfums Exquis',
                    'shop_description' => 'Parfumerie de luxe',
                    'town_id' => 2,
                    'quarter_id' => 38,
                    "shop_profile"=>"shop/profile/shop6.jpg",
                    'product_type' => "1",
                    'shop_gender' => "4",
                    'categories' => [4]
                ]
            ],
            [
                'user' => [
                    'firstName' => 'Michel',
                    'lastName' => 'Nana',
                    'email' => 'michel.nana@example.com',
                    'phone_number' => '677789012',
                    'birthDate' => '1987-02-20',
                    'isWholesaler' => true,
                    'nationality' => 'Camerounaise',
                    'password' => 'password123'
                ],
                'shop' => [
                    'shop_name' => 'Mode Enfants',
                    'shop_description' => 'Vêtements et accessoires pour enfants',
                    'town_id' => 1,
                    'quarter_id' => 7,
                    "shop_profile"=>"shop/profile/shop7.jpg",
                    'product_type' => "0",
                    'shop_gender' => "3",
                    'categories' => [1, 8]
                ]
            ],
            [
                'user' => [
                    'firstName' => 'Anne',
                    'lastName' => 'Tchinda',
                    'email' => 'anne.tchinda@example.com',
                    'phone_number' => '677890123',
                    'birthDate' => '1994-04-05',
                    'isWholesaler' => false,
                    'nationality' => 'Camerounaise',
                    'password' => 'password123'
                ],
                'shop' => [
                    'shop_name' => 'Mèches & Beauty',
                    'shop_description' => 'Mèches et produits capillaires',
                    'town_id' => 2,
                    'quarter_id' => 41,
                    "shop_profile"=>"shop/profile/shop8.jpg",
                    'product_type' => "1",
                    'shop_gender' => "2",
                    'categories' => [5]
                ]
            ],
            [
                'user' => [
                    'firstName' => 'Robert',
                    'lastName' => 'Meka',
                    'email' => 'robert.meka@example.com',
                    'phone_number' => '677901234',
                    'birthDate' => '1989-06-10',
                    'isWholesaler' => true,
                    'nationality' => 'Camerounaise',
                    'password' => 'password123'
                ],
                'shop' => [
                    'shop_name' => 'Sport Fashion',
                    'shop_description' => 'Mode sportive tendance',
                    'town_id' => 1,
                    'quarter_id' => 9,
                    "shop_profile"=>"shop/profile/shop9.jpg",
                    'product_type' => "1",
                    'shop_gender' => "4",
                    'categories' => [7, 1]
                ]
            ],
            [
                'user' => [
                    'firstName' => 'Julie',
                    'lastName' => 'Nguemo',
                    'email' => 'julie.nguemo@example.com',
                    'phone_number' => '678012345',
                    'birthDate' => '1996-08-15',
                    'isWholesaler' => false,
                    'nationality' => 'Camerounaise',
                    'password' => 'password123'
                ],
                'shop' => [
                    'shop_name' => 'Accessoires Chic',
                    'shop_description' => 'Accessoires de mode tendance',
                    'town_id' => 2,
                    'quarter_id' => 44,
                    'product_type' => "1",
                    "shop_profile"=>"shop/profile/shop10.jpg",
                    'shop_gender' => "2",
                    'categories' => [8, 2]
                ]
            ],
            [
                'user' => [
                    'firstName' => 'David',
                    'lastName' => 'Tamba',
                    'email' => 'david.tamba@example.com',
                    'phone_number' => '678123456',
                    'birthDate' => '1992-10-20',
                    'isWholesaler' => true,
                    'nationality' => 'Camerounaise',
                    'password' => 'password123'
                ],
                'shop' => [
                    'shop_name' => 'Homme Élégant',
                    'shop_description' => 'Mode masculine haut de gamme',
                    'town_id' => 1,
                    'quarter_id' => 11,
                    "shop_profile"=>"shop/profile/shop11.jpg",
                    'product_type' => "0",
                    'shop_gender' => "1",
                    'categories' => [1, 4]
                ]
            ],
            [
                'user' => [
                    'firstName' => 'Sarah',
                    'lastName' => 'Ndom',
                    'email' => 'sarah.ndom@example.com',
                    'phone_number' => '678234567',
                    'birthDate' => '1990-12-25',
                    'isWholesaler' => false,
                    'nationality' => 'Camerounaise',
                    'password' => 'password123'
                ],
                'shop' => [
                    'shop_name' => 'Beauty Queen',
                    'shop_description' => 'Institut de beauté et cosmétiques',
                    'town_id' => 2,
                    'quarter_id' => 47,
                    "shop_profile"=>"shop/profile/shop12.jpg",
                    'product_type' => "1",
                    'shop_gender' => "2",
                    'categories' => [6]
                ]
            ],
            [
                'user' => [
                    'firstName' => 'Thomas',
                    'lastName' => 'Ebogo',
                    'email' => 'thomas.ebogo@example.com',
                    'phone_number' => '678345678',
                    'birthDate' => '1988-01-30',
                    'isWholesaler' => true,
                    'nationality' => 'Camerounaise',
                    'password' => 'password123'
                ],
                'shop' => [
                    'shop_name' => 'Chaussures Deluxe',
                    'shop_description' => 'Chaussures de luxe',
                    'town_id' => 1,
                    'quarter_id' => 13,
                    "shop_profile"=>"shop/profile/shop13.jpg",
                    'product_type' => "1",
                    'shop_gender' => "4",
                    'categories' => [3]
                ]
            ],
            [
                'user' => [
                    'firstName' => 'Carine',
                    'lastName' => 'Bella',
                    'email' => 'carine.bella@example.com',
                    'phone_number' => '678456789',
                    'birthDate' => '1993-03-15',
                    'isWholesaler' => false,
                    'nationality' => 'Camerounaise',
                    'password' => 'password123'
                ],
                'shop' => [
                    'shop_name' => 'Mode Africaine',
                    'shop_description' => 'Vêtements traditionnels africains',
                    'town_id' => 2,
                    'quarter_id' => 49,
                    "shop_profile"=>"shop/profile/shop14.jpg",
                    'product_type' => "0",
                    'shop_gender' => "4",
                    'categories' => [1]
                ]
            ],
        ];

        foreach ($shops as $shopData) {
            // Créer l'utilisateur
            $user = new User();
            $user->firstName = $shopData['user']['firstName'];
            $user->lastName = $shopData['user']['lastName'];
            $user->email = $shopData['user']['email'];
            $user->phone_number = $shopData['user']['phone_number'];
            $user->birthDate = $shopData['user']['birthDate'];
            $user->isWholesaler = $shopData['user']['isWholesaler'];
            $user->role_id = 2;
            $user->nationality = $shopData['user']['nationality'];
            $user->password = Hash::make($shopData['user']['password']);
            
            // Simuler les chemins des images CNI
            $user->identity_card_in_front = 'cni/front/9cRyxMsXBhmajuEe8bu5yav86A3jh2ImLw2EOGU3.jpg';
            $user->identity_card_in_back = 'cni/back/DBBFddXLD3o7rgOmJxbmXXKTYWVNm35sqOX4UIv7.webp';
            $user->identity_card_with_the_person = 'cni/person/8RjMqPnBuDotOn3OoCYWzKHVODsulLs7Tdu1sKeG.jpg';
            
            $user->save();

            // Créer la boutique
            $shop = new Shop();
            $shop->shop_name = $shopData['shop']['shop_name'];
            $shop->shop_key = (new generateShopNameService)->generateUniqueShopName($shopData['shop']['shop_name']);
            $shop->shop_description = $shopData['shop']['shop_description'];
            $shop->user_id = $user->id;
            $shop->town_id = $shopData['shop']['town_id'];
            $shop->quarter_id = $shopData['shop']['quarter_id'];
            $shop->product_type = $shopData['shop']['product_type'];
            $shop->shop_gender = $shopData['shop']['shop_gender'];
            $shop->shop_profile = $shopData['shop']['shop_profile'];
            
            $shop->save();

            // Attacher les catégories
            $shop->categories()->attach($shopData['shop']['categories']);

            // Ajouter 3 images par défaut
            for ($i = 1; $i <= 3; $i++) {
                $image = new Image();
                $image->image_path = "shop/images/images{$i}.jpg";
                $image->save();
                $shop->images()->attach($image);
            }
        }
    }
}