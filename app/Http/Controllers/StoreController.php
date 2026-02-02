<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sell;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    public function show($slug)
    {
        $store = User::where('slug', $slug)
            ->where('role', 'store')
            ->with(['categories', 'products.category'])
            ->firstOrFail();

        return view('stores.show', compact('store'));
    }

    public function clientConfig($slug)
    {
        $store = User::where('slug', $slug)
            ->where('role', 'store')
            ->with(['categories', 'products.category'])
            ->firstOrFail();

        return view('stores.client-config', compact('store'));
    }

    public function clientProducts($slug)
    {
        $store = User::where('slug', $slug)
            ->where('role', 'store')
            ->firstOrFail();

        $purchases = collect();

        if (Auth::guard('client')->check()) {
            $client = Auth::guard('client')->user();
            $purchases = Sell::where('client_id', $client->id)
                ->where('user_id', $store->id)
                ->with(['items.product'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('stores.client-products', compact('store', 'purchases'));
    }
}