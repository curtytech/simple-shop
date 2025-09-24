<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Add a product to the cart.
     */
    public function addToCart(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $product = Product::findOrFail($request->product_id);
            $quantity = $request->quantity;

            // Verificar se há estoque suficiente
            if ($product->stock < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Estoque insuficiente. Disponível: ' . $product->stock
                ], 400);
            }

            $client = Auth::guard('client')->user();

            // Obter ou criar carrinho para esta loja
            $cart = $client->getCartForStore($product->user);

            // Verificar se o produto já está no carrinho
            $existingItem = $cart->items()->where('product_id', $product->id)->first();
            $totalQuantity = $existingItem ? $existingItem->quantity + $quantity : $quantity;

            if ($totalQuantity > $product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quantidade total excede o estoque disponível. Disponível: ' . $product->stock
                ], 400);
            }

            $cart->addProduct($product, $quantity);

            return response()->json([
                'success' => true,
                'message' => 'Produto adicionado ao carrinho!',
                'cart_total' => $cart->fresh()->total,
                'cart_quantity' => $cart->fresh()->total_quantity
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao adicionar produto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the quantity of a product in the cart.
     */
    public function updateQuantity(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
        ]);

        try {
            $product = Product::findOrFail($request->product_id);
            $quantity = $request->quantity;

            $client = Auth::guard('client')->user();
            $cart = $client->getCartForStore($product->user);

            if ($quantity == 0) {
                $cart->removeProduct($product);
                $message = 'Produto removido do carrinho!';
            } else {
                // Verificar estoque
                if ($quantity > $product->stock) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Quantidade excede o estoque disponível. Disponível: ' . $product->stock
                    ], 400);
                }

                $cart->updateProductQuantity($product, $quantity);
                $message = 'Quantidade atualizada!';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'cart_total' => $cart->fresh()->total,
                'cart_quantity' => $cart->fresh()->total_quantity
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar carrinho: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove a product from the cart.
     */
    public function removeFromCart(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        try {
            $product = Product::findOrFail($request->product_id);
            $client = Auth::guard('client')->user();

            $cart = $client->getCartForStore($product->user);
            $cart->removeProduct($product);

            return response()->json([
                'success' => true,
                'message' => 'Produto removido do carrinho!',
                'cart_total' => $cart->fresh()->total,
                'cart_quantity' => $cart->fresh()->total_quantity
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover produto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cart contents for a specific store.
     */
    public function getCart(Request $request): JsonResponse
    {
        $request->validate([
            'store_id' => 'required|exists:users,id',
        ]);

        try {
            $client = Auth::guard('client')->user();
            $store = User::findOrFail($request->store_id);

            $cart = $client->getCartForStore($store);
            $cartItems = $cart->items()->with('product')->get();

            return response()->json([
                'success' => true,
                'cart' => [
                    'items' => $cartItems,
                    'total' => $cart->total,
                    'total_quantity' => $cart->total_quantity,
                    'store' => [
                        'id' => $store->id,
                        'name' => $store->name,
                        'slug' => $store->slug
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar carrinho: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear cart for a specific store.
     */
    public function clearCart(Request $request): JsonResponse
    {
        $request->validate([
            'store_id' => 'required|exists:users,id',
        ]);

        try {
            $client = Auth::guard('client')->user();
            $store = User::findOrFail($request->store_id);

            $cart = $client->getCartForStore($store);
            $cart->clear(); // Corrigido: era clearCart(), agora é clear()

            return response()->json([
                'success' => true,
                'message' => 'Carrinho limpo com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao limpar carrinho: ' . $e->getMessage()
            ], 500);
        }
    }
}