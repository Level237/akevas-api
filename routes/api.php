<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TownController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Seller\ShopController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Admin\QuarterController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Shops\ShopListController;
use App\Http\Controllers\Admin\ValidateShopController;
use App\Http\Controllers\Admin\Seller\SellerController;
use App\Http\Controllers\Seller\CreateSellerController;
use App\Http\Controllers\Admin\ValidateSellerController;
use App\Http\Controllers\Products\ProductListController;
use App\Http\Controllers\Seller\CurrentSellerController;
use App\Http\Controllers\Admin\ValidateProductController;
use App\Http\Controllers\Auth\CheckTokenValidityController;
use App\Http\Controllers\Admin\Seller\RecentSellerController;
use App\Http\Controllers\Admin\Seller\ConfirmStatusSellerController;
use App\Http\Controllers\Payment\Coolpay\Shop\SubscribeShopController;
use App\Http\Controllers\Payment\Coolpay\Product\SubscribeProductController;
use App\Http\Controllers\Payment\Coolpay\Product\BuyProductProcessController;
use App\Http\Controllers\Admin\Product\RecentProductController;
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
    Route::get('/shop/{id}',[SellerController::class,'show']);
    Route::get('/categories',[CategoryController::class,'index']);
    Route::get('/towns',[TownController::class,'index']);
    Route::get('/quarters',[QuarterController::class,'index']);
    Route::get('/check/token',[CheckTokenValidityController::class,'checkToken']);
    Route::post("login",[LoginController::class,"login"]);
    Route::post('create/seller',[CreateSellerController::class,'create']);
    Route::get('shop/show/{id}',[ShopController::class,'show']);
    Route::get("products",[ProductListController::class,'index']);
    Route::get("ads/products/{id}",[ProductListController::class,"adsProducts"]);
    Route::get("shops",[ShopListController::class,"index"]);
    Route::get("ads/shops/{id}",[ShopListController::class,"adsShops"]);

Route::middleware(['auth:api','scopes:seller',"isSeller"])->prefix('v1')->group(function(){
    Route::post("init/payment/subscription/product",[SubscribeProductController::class,"initPay"]);
    Route::post('init/payment/subscription/product/pending/{membership_id}/{product_id}/{transaction_ref}',[SubscribeProductController::class,'initPaymentPending']);
    Route::post("check/payment/subscription/product/callback",[SubscribeProductController::class,"paymentCallBack"]);

    Route::post("init/payment/subscription/shop",[SubscribeShopController::class,"initPay"]);
    Route::post('init/payment/subscription/shop/pending/{membership_id}/{shop_id}/{transaction_ref}',[SubscribeShopController::class,'initPaymentPending']);
    Route::apiResource('/shops',ShopController::class);
    Route::apiResource("seller/products",ProductController::class);
});

Route::middleware(['auth:api','scopes:seller'])->prefix('v1')->group(function(){
    Route::get('/current/seller',[CurrentSellerController::class,'currentSeller']);
});

Route::middleware(['auth:api','scopes:admin'])->prefix('v1')->group(function(){
    Route::get('/recent/products',[RecentProductController::class,'index']);
    Route::get('/recent/sellers',[RecentSellerController::class,'recentSeller']);
    Route::post('/shop/confirm/{id}',[ConfirmStatusSellerController::class,'index']);
    Route::apiResource('sellers',SellerController::class);
    Route::apiResource('categories',CategoryController::class);
    Route::apiResource('towns',TownController::class);
    Route::apiResource('quarters',QuarterController::class);
    Route::patch('/product/confirm/{id}',[ValidateProductController::class,'validateProduct']);
});



Route::middleware(['auth:api'])->prefix('v1')->group(function(){
    Route::post("init/payment/buy/product",[BuyProductProcessController::class,"initPayment"]);
    Route::post("payment/pending/buy/product",[BuyProductProcessController::class,"paymentPending"]);
    Route::post("payment/callback/buy/product",[BuyProductProcessController::class,"buyProductCallBack"]);
    Route::get('/current/user',[ProfileController::class,'currentUser']);
    Route::post('/logout',[LogoutController::class,'logout']);
});
