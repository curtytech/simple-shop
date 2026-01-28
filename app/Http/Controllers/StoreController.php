<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
            ->with(['categories', 'products.category'])
            ->firstOrFail();

        return view('stores.client-products', compact('store'));
    }
}