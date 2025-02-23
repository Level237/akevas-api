<?php

namespace App\Http\Controllers\Admin\Delivery;

use App\Models\User;
use App\Models\Delivery;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConfirmStatusDeliveryController extends Controller
{
    public function confirmStatusDelivery($id,Request $request) {
        $delivery = User::find($id);
        $delivery->isDelivery = $request->isDelivery;
        $delivery->save();
        return response()->json(['message' => 'Status de livraison confirmé avec succès']);
    }
}
