<?php

namespace App\Http\Controllers\Seller;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderVariation;
use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;

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
        
        // Récupérer les commandes de produits non variés (orderDetails)
        $ordersFromDetails = Order::whereHas('orderDetails', function($query) use ($shops) {
            $query->whereHas('product', function($productQuery) use ($shops) {
                $productQuery->whereIn('shop_id', $shops);
            });
        })
        ->with(['orderDetails.product.shop', 'user'])
        ->get();

        // Récupérer les commandes de produits variés (orderVariations)
        $ordersFromVariations = Order::whereHas('orderVariations', function($query) use ($shops) {
            $query->whereHas('productVariation', function($productVariationQuery) use ($shops) {
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
        })
        ->with(['orderVariations.productVariation.product.shop', 'orderVariations.variationAttribute.variation.product.shop', 'user'])
        ->get();

        // Combiner et dédupliquer les commandes
        $allOrders = $ordersFromDetails->merge($ordersFromVariations)->unique('id')->sortByDesc('created_at');

        return response()->json(OrderResource::collection($allOrders));
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

        // Récupérer les commandes de produits non variés pour cette boutique
        $ordersFromDetails = Order::whereHas('orderDetails', function($query) use ($shopId) {
            $query->whereHas('product', function($productQuery) use ($shopId) {
                $productQuery->where('shop_id', $shopId);
            });
        })
        ->with(['orderDetails.product.shop', 'user'])
        ->get();

        // Récupérer les commandes de produits variés pour cette boutique
        $ordersFromVariations = Order::whereHas('orderVariations', function($query) use ($shopId) {
            $query->whereHas('productVariation', function($productVariationQuery) use ($shopId) {
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
        })
        ->with(['orderVariations.productVariation.product.shop', 'orderVariations.variationAttribute.variation.product.shop', 'user'])
        ->get();

        // Combiner et dédupliquer les commandes
        $allOrders = $ordersFromDetails->merge($ordersFromVariations)->unique('id')->sortByDesc('created_at');

        return response()->json(OrderResource::collection($allOrders));
    }

    /**
     * Obtenir les statistiques des commandes d'un vendeur
     */
    public function getOrderStats()
    {
        $user = Auth::guard('api')->user();
        
        // Récupérer toutes les boutiques du vendeur
        $shops = Shop::where('user_id', $user->id)->pluck('id');
        
        // Compter les commandes de produits non variés
        $ordersFromDetails = Order::whereHas('orderDetails', function($query) use ($shops) {
            $query->whereHas('product', function($productQuery) use ($shops) {
                $productQuery->whereIn('shop_id', $shops);
            });
        })->get();

        // Compter les commandes de produits variés
        $ordersFromVariations = Order::whereHas('orderVariations', function($query) use ($shops) {
            $query->whereHas('productVariation', function($productVariationQuery) use ($shops) {
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
        })->get();

        // Combiner et dédupliquer pour le total
        $totalOrders = $ordersFromDetails->merge($ordersFromVariations)->unique('id')->count();

        // Calculer le total des revenus
        $totalRevenue = 0;

        // Revenus des produits non variés
        foreach ($ordersFromDetails as $order) {
            foreach ($order->orderDetails as $orderDetail) {
                if (in_array($orderDetail->product->shop_id, $shops->toArray())) {
                    $totalRevenue += $orderDetail->unit_price * $orderDetail->order_product_quantity;
                }
            }
        }

        // Revenus des produits variés
        foreach ($ordersFromVariations as $order) {
            foreach ($order->orderVariations as $orderVariation) {
                if ($orderVariation->productVariation && in_array($orderVariation->productVariation->product->shop_id, $shops->toArray())) {
                    $totalRevenue += $orderVariation->variation_price;
                }
                if ($orderVariation->variationAttribute && in_array($orderVariation->variationAttribute->variation->product->shop_id, $shops->toArray())) {
                    $totalRevenue += $orderVariation->variation_price;
                }
            }
        }

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

    /**
 * Obtenir une commande spécifique par son ID
 */
public function getOrderById($orderId)
{
    $user = Auth::guard('api')->user();
    
    // Récupérer toutes les boutiques du vendeur
    $shops = Shop::where('user_id', $user->id)->pluck('id');
    
    // Récupérer la commande avec tous les détails
    $order = Order::where('id', $orderId)
        ->where(function($query) use ($shops) {
            // Vérifier si la commande contient des produits de ses boutiques
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
        ->with([
            'orderDetails.product.shop',
            'orderVariations.productVariation.product.shop',
            'orderVariations.variationAttribute.variation.product.shop',
            'user',
            'payment'
        ])
        ->first();
    
    if (!$order) {
        return response()->json([
            'success' => false,
            'message' => 'Commande non trouvée ou non autorisée'
        ], 404);
    }
    
    return response()->json(OrderResource::make($order));
}
}
