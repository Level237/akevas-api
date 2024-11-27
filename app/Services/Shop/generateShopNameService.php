<?php

namespace App\Service\Shop;

class generateShopNameService{

    function generateShopName() {
        // Préfixe fixe
        $prefix = 'shop_';

        // Générer une chaîne aléatoire de 5 caractères
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $randomString = substr(str_shuffle(str_repeat($characters, 5)), 0, 5);

        // Combiner le préfixe avec la chaîne aléatoire
        $shopName = $prefix . $randomString;

        return $shopName;
    }
}
