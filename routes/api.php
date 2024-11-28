<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ValidateSellerController;
use App\Http\Controllers\Admin\ValidateShopController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Seller\CreateSellerController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\ShopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post("login",[LoginController::class,"login"]);
Route::post('create/seller',[CreateSellerController::class,'create']);
Route::get('shop/show/{id}',[ShopController::class,'show']);

Route::middleware(['auth:api','scopes:seller'])->prefix('v1')->group(function(){

    Route::apiResource('/shops',ShopController::class);
    Route::apiResource("/products",ProductController::class);
});

Route::middleware(['auth:api','scopes:admin'])->prefix('v1')->group(function(){

 Route::apiResource('categories',CategoryController::class);
 Route::patch('/shop/confirm/{id}',[ValidateShopController::class,'validateShop']);
 Route::patch('/seller/confirm/{id}',[ValidateSellerController::class,'validateSeller']);
});


Route::middleware(['auth:api'])->prefix('v1')->group(function(){
    Route::post('/logout',[LogoutController::class,'logout']);
});
