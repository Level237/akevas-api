<?php

namespace App\Jobs;

use App\Models\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateUniqueShopKeyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    protected $shopId;
    public function __construct($shopId)
    {
        $this->shopId=$shopId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $shop=Shop::find($this->shopId);

        $shopKey = '';
        do {
            // Nettoyer et préparer le préfixe à partir du nom de la boutique
            $prefix = strtolower($shop->shop_name); // Conversion en minuscules
            $prefix = preg_replace('/[^a-z0-9]/', '', $prefix); // Supprime les caractères spéciaux
            $prefix = substr($prefix, 0, 4); // Prend les 4 premiers caractères
            $prefix .= '-'; // Ajoute le tiret
            
            // Générer une chaîne aléatoire de 5 caractères
            $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $randomString = substr(str_shuffle($characters), 0, 5);
            
            // Combiner le préfixe avec la chaîne aléatoire
            $shopKey = $prefix . $randomString;
        } while (Shop::where('shop_key', $shopKey)->exists());
        $shop->shop_key=$shopKey;
        $shop->save();
    }
}
