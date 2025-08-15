<?php

namespace App\Http\Controllers\Seller;

use App\Models\Payment;
use App\Models\Order;
use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PaymentResource;

class OrderListController extends Controller
{
    /**
     * Lister toutes les commandes d'un vendeur
     */
    public function listOrders()
    {
        $user = Auth::guard('api')->user();
        
        // Récupérer toutes les boutiques du vendeur
        $shops = Shop::where('user_id', $user->id)->pluck('id');
        
        // Récupérer toutes les commandes liées aux produits de ces boutiques
        $payments = Payment::whereHas('order', function($query) use ($shops) {
            $query->whereHas('orderDetails', function($orderDetailQuery) use ($shops) {
                $orderDetailQuery->whereHas('product', function($productQuery) use ($shops) {
                    $productQuery->whereIn('shop_id', $shops);
                });
            })
            ->orWhereHas('orderVariations', function($orderVariationQuery) use ($shops) {
                $orderVariationQuery->whereHas('productVariation', function($productVariationQuery) use ($shops) {
                    $productVariationQuery->whereHas('product', function($productQuery) use ($shops) {
                        $productQuery->whereIn('shop_id', $shops);
                    });
                })
                ->orWhereHas('variationAttribute', function($variationAttributeQuery) use ($shops) {
                    $variationAttributeQuery->whereHas('variation', function($productVariationQuery) use ($shops) {
                        $productVariationQuery->whereHas('product', function($productQuery) use ($shops) {
                            $productQuery->whereIn('shop_id', $shops);
                        });
                    });
                });
            });
        })
        ->with(['order.orderDetails.product.shop', 'order.orderVariations.productVariation.product.shop', 'order.orderVariations.variationAttribute.variation.product.shop', 'user'])
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json(PaymentResource::collection($payments));
    }

    /**
     * Lister les commandes d'une boutique spécifique
     */
    public function listOrdersByShop($shopId)
    {
        $user = Auth::guard('api')->user();
        
        // Vérifier que la boutique appartient au vendeur
        $shop = Shop::where('id', $shopId)
                   ->where('user_id', $user->id)
                   ->first();
        
        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Boutique non trouvée ou non autorisée'
            ], 404);
        }

        // Récupérer les commandes de cette boutique spécifique
        $payments = Payment::whereHas('order', function($query) use ($shopId) {
            $query->whereHas('orderDetails', function($orderDetailQuery) use ($shopId) {
                $orderDetailQuery->whereHas('product', function($productQuery) use ($shopId) {
                    $productQuery->where('shop_id', $shopId);
                });
            })
            ->orWhereHas('orderVariations', function($orderVariationQuery) use ($shopId) {
                $orderVariationQuery->whereHas('productVariation', function($productVariationQuery) use ($shopId) {
                    $productVariationQuery->whereHas('product', function($productQuery) use ($shopId) {
                        $productQuery->where('shop_id', $shopId);
                    });
                })
                ->orWhereHas('variationAttribute', function($variationAttributeQuery) use ($shopId) {
                    $variationAttributeQuery->whereHas('variation', function($productVariationQuery) use ($shopId) {
                        $productVariationQuery->whereHas('product', function($productQuery) use ($shopId) {
                            $productQuery->where('shop_id', $shopId);
                        });
                    });
                });
            });
        })
        ->with(['order.orderDetails.product.shop', 'order.orderVariations.productVariation.product.shop', 'order.orderVariations.variationAttribute.variation.product.shop', 'user'])
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json(PaymentResource::collection($payments));
    }

    /**
     * Obtenir les statistiques des commandes d'un vendeur
     */
    public function getOrderStats()
    {
        $user = Auth::guard('api')->user();
        
        // Récupérer toutes les boutiques du vendeur
        $shops = Shop::where('user_id', $user->id)->pluck('id');
        
        // Compter les commandes
        $totalOrders = Payment::whereHas('order', function($query) use ($shops) {
            $query->whereHas('orderDetails', function($orderDetailQuery) use ($shops) {
                $orderDetailQuery->whereHas('product', function($productQuery) use ($shops) {
                    $productQuery->whereIn('shop_id', $shops);
                });
            })
            ->orWhereHas('orderVariations', function($orderVariationQuery) use ($shops) {
                $orderVariationQuery->whereHas('productVariation', function($productVariationQuery) use ($shops) {
                    $productVariationQuery->whereHas('product', function($productQuery) use ($shops) {
                        $productQuery->whereIn('shop_id', $shops);
                    });
                })
                ->orWhereHas('variationAttribute', function($variationAttributeQuery) use ($shops) {
                    $variationAttributeQuery->whereHas('variation', function($productVariationQuery) use ($shops) {
                        $productVariationQuery->whereHas('product', function($productQuery) use ($shops) {
                            $productQuery->whereIn('shop_id', $shops);
                        });
                    });
                });
            });
        })->count();

        // Calculer le total des revenus
        $totalRevenue = Payment::whereHas('order', function($query) use ($shops) {
            $query->whereHas('orderDetails', function($orderDetailQuery) use ($shops) {
                $orderDetailQuery->whereHas('product', function($productQuery) use ($shops) {
                    $productQuery->whereIn('shop_id', $shops);
                });
            })
            ->orWhereHas('orderVariations', function($orderVariationQuery) use ($shops) {
                $orderVariationQuery->whereHas('productVariation', function($productVariationQuery) use ($shops) {
                    $productVariationQuery->whereHas('product', function($productQuery) use ($shops) {
                        $productQuery->whereIn('shop_id', $shops);
                    });
                })
                ->orWhereHas('variationAttribute', function($variationAttributeQuery) use ($shops) {
                    $variationAttributeQuery->whereHas('variation', function($productVariationQuery) use ($shops) {
                        $productVariationQuery->whereHas('product', function($productQuery) use ($shops) {
                            $productQuery->whereIn('shop_id', $shops);
                        });
                    });
                });
            });
        })->sum('price');

        return response()->json([
            'success' => true,
            'message' => 'Statistiques récupérées avec succès',
            'data' => [
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'shops_count' => $shops->count()
            ]
        ]);
    }
}
