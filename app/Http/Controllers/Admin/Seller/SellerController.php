<?php

namespace App\Http\Controllers\Admin\Seller;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SellerResource;

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
    public function show(string $id)
    {
        $shop=Shop::find($id);
        $user=$shop->user_id;
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
