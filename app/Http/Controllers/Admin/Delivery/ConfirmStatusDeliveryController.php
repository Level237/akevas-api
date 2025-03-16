<?php

namespace App\Http\Controllers\Admin\Delivery;

use App\Models\User;
use App\Models\Delivery;
use App\Models\FeedBack;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConfirmStatusDeliveryController extends Controller
{
    public function confirmStatusDelivery($id,Request $request) {

        try{
            $delivery = User::find($id);

            if($request->isDelivery==0){
                $feedBack=new FeedBack;
                $feedBack->user_id=$delivery->id;
                $feedBack->message=$request->message;
                $feedBack->status=0;
                $feedBack->save();
            }
            $delivery->isDelivery = $request->isDelivery;
            $delivery->save();
            return response()->json(['message' => 'Status de livraison confirmé avec succès']);
        }catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue',
                'error' => $e->getMessage()
            ], 500);
        }
        
    }
}
