<?php

namespace App\Http\Controllers\Seller;

use Carbon\Carbon;
use App\Models\Shop;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\SubscriptionUser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BoostShopController extends Controller
{
    public function boost(Request $request)
    {

        try {
            $user = Auth::guard('api')->user();
            $shop = Shop::where('user_id', $user->id)->first();
            $subscriptionPlan = Subscription::findOrFail($request->subscription_id);

            if (!$shop) {
                return response()->json(['message' => 'Boutique introuvable'], 404);
            }

            $priceInCoins = (int) $subscriptionPlan->subscription_price;
            if ($shop->coins < $priceInCoins) {
                return response()->json(['message' => 'Solde de coins insuffisant'], 402);
            }

            $startDate = ($shop->isSubscribe && $shop->subscription_ends_at > now())
                ? Carbon::parse($shop->subscription_ends_at)
                : Carbon::now();
            $expirationDate = $startDate->addDays($subscriptionPlan->subscription_duration)
                ->setTimezone('Africa/Douala');


            $shop->subscription_id = $subscriptionPlan->id;
            $shop->subscription_starts_at = Carbon::now(); // La transaction se fait maintenant
            $shop->subscription_ends_at = $expirationDate;
            $shop->isSubscribe = 1;

            $shop->coins -= $priceInCoins;

            if ($shop->shop_level == 3) {
                $shop->shop_level = 4;

            }
            if ($subscriptionPlan->id == 3) {
                $shop->coins += 500;
            }

            $shop->save();

            $payment = Payment::create([
                'payment_type' => "coins",
                'price' => $priceInCoins,
                'payment_of' => "Boostage boutique : " . $subscriptionPlan->subscription_name,
                'user_id' => $user->id,
                'status' => 'completed'
            ]);
            SubscriptionUser::create([
                'user_id' => $user->id,
                'subscription_id' => $subscriptionPlan->id,
                'expire_at' => $expirationDate,
                'payment_id' => $payment->id,
                'status' => 1
            ]);

            return response()->json([
                "status" => 1,
                "message" => "Boutique boostée avec succès !",
                "expires_at" => $expirationDate->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            return response()->json(["status" => $e->getMessage()]);
        }

    }
}
