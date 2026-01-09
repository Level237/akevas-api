<?php

namespace App\Console;

use App\Jobs\JobCheckSubscriptionProduct;
use App\Mail\StockReminderMail;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        //$schedule->job(new JobCheckSubscriptionProduct)->hourly();
        // $schedule->command('inspire')->hourly();
        //$schedule->command('log:clear')->everyTwoMinutes();
        $schedule->command('queue:work --stop-when-empty')->everyMinute();

        $schedule->call(function () {
            // On charge les produits avec leurs variations et les attributs des variations
            $sellers = User::where('role', 'seller')
                ->with(['products.variations.color', 'products.variations.attributesVariation.attributeValue'])
                ->get();

            foreach ($sellers as $seller) {
                $stockData = [];

                foreach ($seller->products as $product) {
                    if ($product->variations->isEmpty()) {
                        // CAS 1 : Produit Simple
                        $stockData[] = [
                            'id' => $product->id,
                            'name' => $product->product_name,
                            'qty' => $product->product_quantity,
                            'url' => $product->product_url
                        ];
                    } else {
                        // CAS 2 : Produit à Variations
                        foreach ($product->variations as $variation) {
                            $colorName = $variation->color->value ?? '';

                            if ($variation->attributesVariation->isEmpty()) {
                                // Couleur uniquement
                                $stockData[] = [
                                    'id' => $product->id,
                                    'name' => "{$product->product_name} ({$colorName})",
                                    'qty' => $variation->quantity,
                                    'url' => $product->product_url
                                ];
                            } else {
                                // Couleur + Attributs (Taille, etc.)
                                foreach ($variation->attributesVariation as $attr) {
                                    $attrValue = $attr->attributeValue->value ?? '';
                                    $stockData[] = [
                                        'id' => $product->id,
                                        'name' => "{$product->product_name} ({$colorName} - {$attrValue})",
                                        'qty' => $attr->quantity,
                                        'url' => $product->product_url
                                    ];
                                }
                            }
                        }
                    }
                }

                if (!empty($stockData)) {
                    Mail::to($seller->email)->queue(new StockReminderMail($seller, $stockData));
                }
            }
        })->weeklyOn(0, '15:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
