<?php

namespace App\Http\Controllers\Admin\Seller;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SellerResource;
use App\Services\Shop\CreateVisitShopService;
use Illuminate\Support\Facades\Cache;

class SellerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SellerResource::collection(User::where('role_id', 2)->orderBy('created_at', 'desc')->get());
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
    public function show(string $id, Request $request)
    {

        (new CreateVisitShopService())->visit($id, $request->ip(), $request->userAgent());

        // 2. Optimisation : Récupérer le Shop ET l'User en UNE SEULE requête
        $shop = Cache::remember("shop.profile.{$id}", now()->addHours(1), function () use ($id) {
            // ->with('user') charge l'utilisateur en même temps que le shop (Eager Loading)
            return Shop::with('user')->findOrFail($id);
        });

        // 3. Retourner la Resource de l'utilisateur associé au shop
        return response()->json(SellerResource::make($shop->user));
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
