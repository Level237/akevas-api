<?php

namespace App\Http\Controllers\Seller;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\NewSellerRequest;
use App\Services\Shop\generateShopNameService;
use App\Models\Image;
class CreateSellerController extends Controller
{
    public function create(Request $request){

        
           if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('images','public');
        }

        return response()->json(['success' => true, 'path' => $path]);
    } else {
        return response()->json(['success' => false, 'message' => 'Aucun fichier reçu']);
    }
            
    }
    
}