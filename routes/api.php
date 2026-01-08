<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Jobs\SendNewOrderNotificationJob;
use App\Http\Controllers\Admin\TownController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Coins\CallbackPayment;
use App\Http\Controllers\Seller\ShopController;
use App\Http\Controllers\User\SearchController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\ListCategoryController;
use App\Http\Controllers\Admin\QuarterController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\GetAttributesController;
use App\Http\Controllers\User\StatShopController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Shops\ShopListController;
use App\Http\Controllers\User\ListOrderController;
use App\Http\Controllers\User\ShowOrderController;
use App\Http\Controllers\Admin\GiveCoinsController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Seller\BoostShopController;
use App\Http\Controllers\Seller\CatalogueController;
use App\Http\Controllers\Seller\OrderListController;
use App\Http\Controllers\SendNotificationController;
use App\Http\Controllers\User\RecentOrderController;
use App\Http\Controllers\Admin\OrderDetailController;
use App\Http\Controllers\Admin\ValidateShopController;
use App\Http\Controllers\Coins\HandleVerifyController;
use App\Http\Controllers\Product\ListReviewController;
use App\Http\Controllers\Admin\Seller\SellerController;
use App\Http\Controllers\Coins\HandleWebhookController;
use App\Http\Controllers\Product\ProductListController;
use App\Http\Controllers\Seller\CreateSellerController;
use App\Http\Controllers\Seller\UpdateSellerController;
use App\Http\Controllers\User\MakeReviewShopController;
use App\Http\Controllers\Admin\ValidateSellerController;
use App\Http\Controllers\Gender\CurrentGenderController;
use App\Http\Controllers\Seller\CurrentSellerController;
use App\Http\Controllers\Shops\GetProfileShopController;
use App\Http\Controllers\Admin\Stat\ActiveStatController;
use App\Http\Controllers\Admin\ValidateProductController;
use App\Http\Controllers\Delivery\OrderHistoryController;
use App\Http\Controllers\Delivery\StatOverviewController;
use App\Http\Controllers\Product\DetailProductController;
use App\Http\Controllers\Shops\CheckShopStatusController;
use App\Http\Controllers\Category\CategoryByUrlController;
use App\Http\Controllers\Payment\Stripe\PaymentController;
use App\Http\Controllers\Product\SimilarProductController;
use App\Http\Controllers\Shops\GetShowModalShopController;
use App\Http\Controllers\Admin\Customer\CustomerController;
use App\Http\Controllers\Admin\Delivery\DeliveryController;
use App\Http\Controllers\Auth\CheckTokenValidityController;
use App\Http\Controllers\Category\CategoryFilterController;
use App\Http\Controllers\Delivery\CreateDeliveryController;
use App\Http\Controllers\Delivery\GetOrderOfTownController;
use App\Http\Controllers\Delivery\OrderCompletedController;
use App\Http\Controllers\Delivery\UpdateDeliveryController;
use App\Http\Controllers\Seller\ListNotificationController;
use App\Http\Controllers\Seller\ListSubscriptionController;
use App\Http\Controllers\Seller\ShowPaymentByRefController;
use App\Http\Controllers\Seller\ShowSubscriptionController;
use App\Http\Controllers\User\MakeCommentProductController;
use App\Http\Controllers\Product\PublishedProductController;
use App\Http\Controllers\Admin\Customer\ListOrdersController;
use App\Http\Controllers\Admin\Product\ListProductController;
use App\Http\Controllers\Admin\Seller\RecentSellerController;
use App\Http\Controllers\Coins\ValidatePaymentCoinController;
use App\Http\Controllers\Delivery\TakeOrderProcessController;
use App\Http\Controllers\Product\ProductByCategoryController;
use App\Http\Controllers\Admin\Feedback\ListFeedbackController;
use App\Http\Controllers\Admin\Product\RecentProductController;
use App\Http\Controllers\Admin\Stat\ActiveSellerStatController;
use App\Http\Controllers\CheckIfInputExistInDatabaseController;
use App\Http\Controllers\Delivery\GetPreferenceOrderController;
use App\Http\Controllers\Product\Payment\InitPaymentController;
use App\Http\Controllers\User\ShowPaymentByReferenceController;
use App\Http\Controllers\Admin\Reviews\ListShopReviewController;
use App\Http\Controllers\Admin\Delivery\RecentDeliveryController;
use App\Http\Controllers\Admin\Stat\ActiveDeliveryStatController;
use App\Http\Controllers\Payment\Coolpay\Product\PayinController;
use App\Http\Controllers\Product\Payment\SucessPaymentController;
use App\Http\Controllers\Product\ResizeAllProductImageController;
use App\Http\Controllers\User\ControlPaymentStatusByRefController;
use App\Http\Controllers\Admin\Seller\ConfirmStatusSellerController;
use App\Http\Controllers\Payment\Coolpay\Product\CheckPayinController;
use App\Http\Controllers\Payment\Coolpay\Shop\SubscribeShopController;
use App\Http\Controllers\Admin\Delivery\ConfirmStatusDeliveryController;
use App\Http\Controllers\Admin\Reviews\DeclineOrValidateReviewController;
use App\Http\Controllers\Payment\Coolpay\Product\SubscribeProductController;
use App\Http\Controllers\Product\Payment\PaymentProductProcessingController;
use App\Http\Controllers\Shops\ListReviewController as ShopReviewController;
use App\Http\Controllers\Admin\Reviews\DeclineOrValidateShopReviewController;
use App\Http\Controllers\Product\Payment\HandleWebhookProductPaymentController;
use App\Http\Controllers\Delivery\ProfileController as DeliveryProfileController;
use App\Http\Controllers\Admin\RecentOrderController as AdminRecentOrderController;
use App\Http\Controllers\Coins\InitPaymentController as InitCoinsPaymentController;
use App\Http\Controllers\Delivery\ShowOrderController as DeliveryShowOrderController;
use App\Http\Controllers\Admin\Reviews\ListReviewController as AdminListReviewsController;
use App\Http\Controllers\Auth\ForgotPasswordController;

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



Route::post("/password/forgot", [ForgotPasswordController::class, 'sendForgotPasswordOtp']);

Route::post("/password/verify-otp", [ForgotPasswordController::class, 'verifyOtp']);

Route::post("/password/reset", [ForgotPasswordController::class, 'resetPassword']);
Route::post("check/shop/status", [CheckShopStatusController::class, 'checkShopStatus']);

Route::get("catalogue/{shop_key}", [CatalogueController::class, 'index']);

Route::get("resize/products", [ResizeAllProductImageController::class, "resizeAllProductImage"]);

Route::get('/filter/products/{arrayId}', [CategoryFilterController::class, 'filter']);
Route::get('/filter/categories/{arraySubCategoryId}', [CategoryFilterController::class, 'getCategoryBySubCategory']);
//Status for notchpay
Route::post('/get/payment/status', [HandleVerifyController::class, 'getPaymentStatus']);

//Status for coolpay
Route::post('/status/payin', [CheckPayinController::class, 'checkStatus']);
Route::post('/status/payin/coins', [InitCoinsPaymentController::class, 'checkStatusCoins']);

Route::post('/notchpay/coins/webhook', [HandleWebhookController::class, 'handleWebhook']);
Route::post('/notchpay/product/webhook', [HandleWebhookProductPaymentController::class, 'handleWebhook']);
Route::get('/callback/payment', [CallbackPayment::class, 'callbackPayment']);
Route::get('/get/profile/shop', [GetProfileShopController::class, 'getProfile']);
Route::get("/get/modal/shop", [GetShowModalShopController::class, 'showRandom']);
Route::get("/search/{query}/{userId}", [SearchController::class, 'search']);
Route::get('/category/by-url/{url}', [CategoryByUrlController::class, 'index']);
Route::get('/list/reviews/{productId}', [ListReviewController::class, 'index']);
Route::get('/list/reviews/shop/{shopId}', [ShopReviewController::class, 'index']);
Route::get('/product/by-category/{url}', [ProductByCategoryController::class, 'index']);
Route::get('/send/notification', [SendNotificationController::class, 'sendNotification']);
Route::post('create/delivery', [CreateDeliveryController::class, 'create']);
Route::get('/category/gender/{id}', [ListCategoryController::class, 'getCategoriesByGender']);
Route::get('/get/category/by-gender/{id}', [ListCategoryController::class, 'showCategoryByGender']);
Route::get('/get/sub-categories/{arrayIds}/{id}', [ListCategoryController::class, 'getSubCategoriesByParentId']);
Route::post('/check/email-and-phone-number', [CheckIfInputExistInDatabaseController::class, 'checkEmailAndPhoneNumber']);
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/categories/with-parent-id-null', [ListCategoryController::class, 'getCategoryWithParentIdNull']);
Route::get('/shop/{id}', [SellerController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/towns', [TownController::class, 'index']);
Route::get('/quarters', [QuarterController::class, 'index']);
Route::get('/check/token', [CheckTokenValidityController::class, 'checkToken']);
Route::post('/login', [LoginController::class, 'login']);
Route::get('/refresh/token', [AuthController::class, 'refresh']);
Route::post('create/seller', [CreateSellerController::class, 'create']);
Route::get('shop/show/{id}', [ShopController::class, 'show']);
Route::get("home/products", [ProductListController::class, 'index']);
Route::get("ads/products/{id}", [ProductListController::class, "adsProducts"]);
Route::get("/home/shops", [ShopListController::class, "index"]);
Route::get("ads/shops/{id}", [ShopListController::class, "adsShops"]);
Route::get("all/shops", [ShopListController::class, "all"]);
Route::get("current/gender/{id}", [CurrentGenderController::class, "show"]);
Route::get('/list/subscriptions', [ListSubscriptionController::class, 'index']);
Route::get('/show/subscription/{id}', [ShowSubscriptionController::class, 'show']);
Route::get("product/detail/{product_url}", [DetailProductController::class, "index"]);
Route::get("/similar/products/{id}", [SimilarProductController::class, "getSimilarProducts"]);
Route::get("all/products", [ProductListController::class, "allProducts"]);
Route::get("/attributes/value/{id}", [GetAttributesController::class, 'getValue']);
Route::get("/all/genders", [CurrentGenderController::class, "all"]);
Route::get("/get/sales/{shopId}", [ShopController::class, "countShopSales"]);
Route::get('/attributes/value/by/group/{id}', [ListCategoryController::class, "getAttributeValueByAttributeId"]);
Route::get('/categories/attributes', [ListCategoryController::class, "getCategoriesWithAttributes"]);

Route::middleware(["auth:api", 'scopes:seller', "isSeller"])->prefix('v1')->group(function () {
    Route::post("init/payment/subscription/product", [SubscribeProductController::class, "initPay"]);
    Route::post('init/payment/subscription/product/pending/{membership_id}/{product_id}/{transaction_ref}', [SubscribeProductController::class, 'initPaymentPending']);
    Route::post("check/payment/subscription/product/callback", [SubscribeProductController::class, "paymentCallBack"]);
    Route::get('/show/payment/coins/{ref}', [ShowPaymentByRefController::class, 'show']);
    Route::post('validate/payment/coins', [ValidatePaymentCoinController::class, 'handle']);
    Route::post("init/payment/coins", [InitCoinsPaymentController::class, "payin"]);
    Route::post("init/payment/subscription/shop", [SubscribeShopController::class, "initPay"]);
    Route::post('init/payment/subscription/shop/pending/{membership_id}/{shop_id}/{transaction_ref}', [SubscribeShopController::class, 'initPaymentPending']);
    Route::apiResource('/shops', ShopController::class);
    Route::apiResource("seller/products", ProductController::class);
    Route::post('update/categories', [UpdateSellerController::class, 'updateCategories']);
    Route::post('update/images', [UpdateSellerController::class, 'updateGalerieImages']);
    Route::post("/boost/shop", [BoostShopController::class, 'boost']);

    Route::post('/put/in/trash/{id}', [ProductController::class, 'putInTrash']);
    Route::get('/trash/products', [ProductController::class, 'productListOfTrash']);
    Route::get("/reject/products", [ProductController::class, "productListOfRejected"]);
    Route::post("/restore/product/{id}", [ProductController::class, 'restoreProduct']);
    // Routes pour la gestion des commandes du vendeur
    Route::get('/seller/orders', [OrderListController::class, 'listOrders']);
    Route::get('/seller/orders/shop/{shopId}', [OrderListController::class, 'listOrdersByShop']);
    Route::get('/seller/orders/stats', [OrderListController::class, 'getOrderStats']);
    Route::get('/seller/order/{id}', [OrderListController::class, 'getOrderById']);

    Route::get('/seller/edit/product/{url}', [ProductController::class, 'getEditProduct']);
});

Route::middleware(['auth:api', 'scopes:delivery'])->prefix('v1')->group(function () {

    Route::post("/delivery/update/docs", [UpdateDeliveryController::class, 'updateDocuments']);
});

Route::middleware(["auth:api", 'scopes:seller'])->prefix('v1')->group(function () {
    Route::get('/current/seller', [CurrentSellerController::class, 'currentSeller']);
    Route::post('update/seller', [CurrentSellerController::class, 'updateSeller']);
    Route::get('/seller/notifications', [ListNotificationController::class, 'list']);
    Route::get("/seller/recents/notifications", [ListNotificationController::class, 'recentNotification']);
    Route::get("/seller/get/notification/{id}", [ListNotificationController::class, 'getNotification']);
    Route::post("/update/docs", [UpdateSellerController::class, 'updateDocuments']);
});

Route::middleware(["auth:api", 'scopes:admin'])->prefix('v1')->group(function () {
    Route::post('/decline/or/validate/{reviewId}/{status}', [DeclineOrValidateReviewController::class, 'declineOrValidate']);
    Route::post('/decline/or/validate/shop/review/{reviewId}/{status}', [DeclineOrValidateShopReviewController::class, 'declineOrValidate']);
    Route::get('/recent/products', [RecentProductController::class, 'index']);
    Route::get('/recent/sellers', [RecentSellerController::class, 'recentSeller']);
    Route::get('/recent/deliveries', [RecentDeliveryController::class, 'recentDelivery']);
    Route::get('admin/products', [ListProductController::class, 'index']);
    Route::get("/admin/reviews", [AdminListReviewsController::class, 'index']);
    Route::apiResource('admin/deliveries', DeliveryController::class);
    Route::post('/shop/confirm/{id}', [ConfirmStatusSellerController::class, 'index']);
    Route::post('/delivery/confirm/{id}', [ConfirmStatusDeliveryController::class, 'confirmStatusDelivery']);
    Route::apiResource('sellers', SellerController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::delete('admin/category/{id}', [CategoryController::class, 'destroy']);
    Route::apiResource('towns', TownController::class);
    Route::apiResource('quarters', QuarterController::class);
    Route::apiResource('admin/customers', CustomerController::class);
    Route::post("give/coins", [GiveCoinsController::class, 'giveCoins']);
    Route::patch('/product/confirm/{id}', [ValidateProductController::class, 'validateProduct']);
    Route::get('/admin/active/stats', [ActiveStatController::class, 'activeStat']);
    Route::get('/admin/active/seller/stats', [ActiveSellerStatController::class, 'activeSellerStat']);
    Route::get('/admin/active/delivery/stats', [ActiveDeliveryStatController::class, 'activeDeliveryStat']);
    Route::get('/admin/list/feedbacks', [ListFeedbackController::class, 'index']);
    Route::get('/admin/list/shop/reviews', [ListShopReviewController::class, 'index']);
    Route::get('admin/orders', [ListOrdersController::class, 'listOrders']);
    Route::post("add/shop", [CreateSellerController::class, 'create']);
    Route::post('/published/product/{id}', [PublishedProductController::class, 'publishedProduct']);
    Route::get('/admin/order/{id}', [OrderDetailController::class, 'detail']);
    Route::get('/admin/recent/orders', [AdminRecentOrderController::class, 'recentOrder']);
    Route::get('/admin/all/categories', [CategoryController::class, 'all']);
    Route::get('/admin/all/categories', [CategoryController::class, 'all']);
    Route::post('/admin/add/category', [CategoryController::class, 'store']);
    Route::get('/admin/category/{id}', [CategoryController::class, 'getCategory']);
    Route::post('update/category/{id}', [CategoryController::class, 'update']);
});

Route::middleware(["auth:api", 'scopes:customer'])->prefix('v1')->group(function () {
    Route::get('/recent/orders', [RecentOrderController::class, 'recentOrders']);
    Route::get('user/show/order/{id}', [ShowOrderController::class, 'showOrder']);
    Route::get('/list/orders', [ListOrderController::class, 'listOrder']);
    Route::get('/current/stats', [StatShopController::class, 'currentStats']);
});


Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    Route::get('/orders/towns', [GetOrderOfTownController::class, 'getOrdersByTown']);
    //Route::get('init/pay/coolpay',[BuyProductProcessController::class,'initPaymentCoolpay']);
    Route::post("init/payment/buy/product", [InitPaymentController::class, "initPayment"]);

    Route::get("/recents/histories", [SearchController::class, "recentHistory"]);
    Route::get('/current/user', [ProfileController::class, 'currentUser']);
    Route::post('/update/user', [ProfileController::class, 'updateUser']);
    Route::post('/logout', [LogoutController::class, 'logout']);
    Route::post('/control/payment', [ControlPaymentStatusByRefController::class, 'control']);
    Route::post('/payment/product', [PaymentProductProcessingController::class, 'handlePaymentProduct']);
    Route::post('/payment/stripe', [PaymentController::class, 'pay']);
    Route::get('/check-auth', [CheckTokenValidityController::class, 'checkIsAuthenticated']);
    Route::post('/make/comment/product/{product_id}', [MakeCommentProductController::class, 'makeCommentProduct']);
    Route::post('/make/comment/shop/{shop_id}', [MakeReviewShopController::class, 'makeCommentShop']);
    Route::post('/success/payment', [SucessPaymentController::class, 'successPayment']);
    Route::get("/show/payment/{ref}", [ShowPaymentByReferenceController::class, "show"]);
    Route::post('/payin', [PayinController::class, 'payin']);
});

Route::middleware(["auth:api", 'scopes:delivery'])->prefix('v1')->group(function () {

    Route::get('/preference/orders', [GetPreferenceOrderController::class, 'getPreferenceOrders']);
    Route::get('/current/delivery', [DeliveryProfileController::class, 'currentDelivery']);
    Route::get('/show/order/{id}', [DeliveryShowOrderController::class, 'showOrder']);
    Route::get('/orders/quarter/{residence_id}', [GetOrderOfTownController::class, 'getOrderInQuarter']);
    Route::post('/take/order/{id}', [TakeOrderProcessController::class, 'takeOrder']);
    Route::post('/cancel/order/{id}', [OrderCompletedController::class, 'cancelOrder']);
    Route::get('/orders/history', [OrderHistoryController::class, 'getOrderHistory']);
    Route::post('/order/completed/{id}/{duration}', [OrderCompletedController::class, 'orderCompleted']);
    Route::get('/delivery/stats/overview', [StatOverviewController::class, 'getStatOverview']);
    Route::get('/delivery/stats/by-day', [StatOverviewController::class, 'statsByDay']);
});


Route::prefix('auth/google')->group(function () {

    Route::get('/', [SocialAuthController::class, 'redirectToGoogle']);
    Route::get('callback', [SocialAuthController::class, "handleGoogleCallback"]);
});
