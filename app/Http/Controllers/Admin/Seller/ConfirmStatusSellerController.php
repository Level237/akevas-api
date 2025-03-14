<?php

namespace App\Http\Controllers\Admin\Seller;

use App\Models\Shop;
use App\Models\User;
use App\Models\FeedBack;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConfirmStatusSellerController extends Controller
{
    public function index($shop_id, Request $request) {
        try {
            $shop = Shop::find($shop_id);
            
            if (!$shop) {
                return response()->json(['message' => 'Boutique non trouvée'], 404);
            }
            if($request->state=="2"){
                $feedBack=new FeedBack;
                $feedBack->user_id=$request->user_id;
                $feedBack->message=$request->message;
                $feedBack->status=0;
                $feedBack->save();
            }
            $shop->isPublished = $request->isPublished;
            $shop->shop_level=$request->shop_level;
            $shop->state = $request->state;
            
            if ($shop->save()) {
                $user = User::find($shop->user_id);
                $user->isSeller = $request->isSeller;
                $user->save();
                
                return response()->json(['message' => 'success']);
            }
            
            return response()->json(['message' => 'Échec de la sauvegarde'], 500);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
