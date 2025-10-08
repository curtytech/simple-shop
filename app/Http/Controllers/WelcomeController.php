<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        // Buscar lojas (usuários com role 'store')
        $stores = User::where('role', 'store')
                      ->with(['products', 'categories'])
                      ->get();
        
        // Calcular estatísticas
        $totalProducts = $stores->sum(function($store) {
            return $store->products->count();
        });
        
        $totalCategories = $stores->sum(function($store) {
            return $store->categories->count();
        });
        
        // Buscar categorias populares (com contagem de produtos)
        $popularCategories = Category::withCount('products')
                                   ->orderBy('products_count', 'desc')
                                   ->limit(8)
                                   ->get();
        
        return view('welcome', compact('stores', 'totalProducts', 'totalCategories', 'popularCategories'));
    }
}