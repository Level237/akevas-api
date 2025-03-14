<?php

namespace App\Http\Controllers\Seller;

use App\Models\Shop;
use App\Models\FeedBack;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UpdateSellerController extends Controller
{
        
    public function updateDocuments(Request $request)
    {
        try {
            $seller=Auth::guard("api")->user();
            $shop = Shop::where('user_id', $seller->id)->firstOrFail();
            $feedBack=FeedBack::where('user_id', $seller->id)->firstOrFail();
            // Update shop profile (logo) if provided
            if ($request->hasFile('shop_profile')) {
                // Delete old shop profile if exists
                if ($shop->shop_profile) {
                    Storage::disk('public')->delete($shop->shop_profile);
                }
                $shop->shop_profile = $request->file('shop_profile')->store('shop/profile', 'public');
                $shop->save();
            }

            // Update identity documents if provided
            if ($request->hasFile('identity_card_in_front')) {
                if ($seller->identity_card_in_front) {
                    Storage::disk('public')->delete($seller->identity_card_in_front);
                }
                $seller->identity_card_in_front = $request->file('identity_card_in_front')->store('cni/front', 'public');
            }

            if ($request->hasFile('identity_card_in_back')) {
                if ($seller->identity_card_in_back) {
                    Storage::disk('public')->delete($seller->identity_card_in_back);
                }
                $seller->identity_card_in_back = $request->file('identity_card_in_back')->store('cni/back', 'public');
            }

            if ($request->hasFile('identity_card_with_the_person')) {
                if ($seller->identity_card_with_the_person) {
                    Storage::disk('public')->delete($seller->identity_card_with_the_person);
                }
                $seller->identity_card_with_the_person = $request->file('identity_card_with_the_person')->store('cni/person', 'public');
            }
            $feedBack->status=1;
            $feedBack->save();
            $seller->save();

            return response()->json([
                'message' => 'Documents updated successfully',
                'success' => true
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}
