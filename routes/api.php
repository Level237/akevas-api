<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ValidateProductController;
use App\Http\Controllers\Admin\ValidateSellerController;
use App\Http\Controllers\Admin\ValidateShopController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Payment\Coolpay\Product\SubscribeProductController;
use App\Http\Controllers\Payment\Coolpay\Shop\SubscribeShopController;
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

Route::middleware(['auth:api','scopes:seller',"isSeller"])->prefix('v1')->group(function(){

    Route::post("init/payment/subscription/product",[SubscribeProductController::class,"initPay"]);
    Route::post('init/payment/subscription/product/pending/{membership_id}/{product_id}/{transaction_ref}',[SubscribeProductController::class,'initPaymentPending']);
    Route::post("check/payment/subscription/product/callback",[SubscribeProductController::class,"paymentCallBack"]);

    Route::post("init/payment/subscription/shop",[SubscribeShopController::class,"initPay"]);
    Route::post('init/payment/subscription/shop/pending/{membership_id}/{shop_id}/{transaction_ref}',[SubscribeShopController::class,'initPaymentPending']);
    Route::apiResource('/shops',ShopController::class);
    Route::apiResource("/products",ProductController::class);
});

Route::middleware(['auth:api','scopes:admin'])->prefix('v1')->group(function(){

 Route::apiResource('categories',CategoryController::class);
 Route::patch('/shop/confirm/{id}',[ValidateShopController::class,'validateShop']);
 Route::patch('/seller/confirm/{id}',[ValidateSellerController::class,'validateSeller']);
 Route::patch('/product/confirm/{id}',[ValidateProductController::class,'validateProduct']);
});



Route::middleware(['auth:api'])->prefix('v1')->group(function(){
    Route::post('/logout',[LogoutController::class,'logout']);
});
