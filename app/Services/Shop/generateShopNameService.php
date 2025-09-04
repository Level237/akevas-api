<?php

namespace App\Services\Shop;

use App\Models\Shop;

function generateUniqueShopName(string $shopName) {
        $shopKey = '';
        do {
            // Nettoyer et préparer le préfixe à partir du nom de la boutique
            $prefix = strtolower($shopName); // Conversion en minuscules
            $prefix = preg_replace('/[^a-z0-9]/', '', $prefix); // Supprime les caractères spéciaux
            $prefix = substr($prefix, 0, 4); // Prend les 4 premiers caractères
            $prefix .= '-'; // Ajoute le tiret
            
            // Générer une chaîne aléatoire de 5 caractères
            $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $randomString = substr(str_shuffle($characters), 0, 5);
            
            // Combiner le préfixe avec la chaîne aléatoire
            $shopKey = $prefix . $randomString;
        } while (Shop::where('shop_key', $shopKey)->exists());

        return $shopKey;
    }
