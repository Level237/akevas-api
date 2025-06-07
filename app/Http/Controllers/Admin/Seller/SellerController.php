<?php

namespace App\Http\Controllers\Admin\Seller;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SellerResource;
use App\Services\Shop\CreateVisitShopService;
use Illuminate\Support\Facades\Log;
class SellerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         return SellerResource::collection(User::where('role_id',2)->orderBy('created_at', 'desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id,Request $request)
    {
        $shop=Shop::find($id);
        $user=$shop->user_id;
        $visits=(new CreateVisitShopService())->visit($id,$request->ip(),$request->userAgent());
        
        Log::info('Payment failed for user', [
            "user"=>$request->userAgent(),
           
        ]);
         return SellerResource::make(User::find($user));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
