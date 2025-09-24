<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('client')->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você precisa estar logado para realizar esta ação.',
                    'requires_auth' => true
                ], 401);
            }
            
            return redirect()->route('client.login');
        }

        return $next($request);
    }
}