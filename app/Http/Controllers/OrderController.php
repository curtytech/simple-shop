<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'store_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $client = auth('client')->user();
        $orders = Order::query()
            ->with('product:id,name')
            ->where('client_id', $client->id)
            ->where('user_id', $validated['store_id'])
            ->latest()
            ->limit(30)
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders->map(fn (Order $order): array => [
                'id' => $order->id,
                'product' => $order->product?->name,
                'quantity' => $order->quantity,
                'total_price' => (float) $order->total_price,
                'status' => $order->status,
                'approved' => $order->approved,
                'payment_status' => $order->payment_status,
            ]),
        ]);
    }

    public function submit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'store_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $client = auth('client')->user();
        $store = User::findOrFail($validated['store_id']);
        $cart = $client->getCartForStore($store);
        $items = $cart->items()->with('product')->get();
        $orderItems = $items->filter(fn ($item): bool => $item->type_of_sell === 'encomenda');

        if ($orderItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Não há itens de encomenda no carrinho.',
            ], 422);
        }

        foreach ($orderItems as $item) {
            if (!in_array('encomenda', $item->product->type_of_sell ?? [], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Uma das encomendas não está mais disponível. Atualize o carrinho.',
                ], 422);
            }
        }

        $orders = DB::transaction(function () use ($orderItems, $client, $store): array {
            $createdOrders = [];

            foreach ($orderItems as $item) {
                $createdOrders[] = Order::create([
                    'user_id' => $store->id,
                    'client_id' => $client->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'total_price' => $item->quantity * $item->price,
                    'approved' => false,
                    'status' => 'pendente',
                    'payment_status' => 'unpaid',
                ]);

                $item->delete();
            }

            return $createdOrders;
        });

        return response()->json([
            'success' => true,
            'message' => 'Encomenda enviada à loja. O pagamento ficará disponível após a aprovação.',
            'order_ids' => array_map(fn (Order $order): int => $order->id, $orders),
        ]);
    }
}
