<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ClientAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('clients.login');
    }

    public function showRegisterForm()
    {
        return view('clients.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('client')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                'message' => 'Login realizado com sucesso!',
                'client' => Auth::guard('client')->user()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Credenciais inválidas.'
        ], 401);
    }

    public function register(Request $request)
    {
        // Debug: log dos dados recebidos
        Log::info('Dados recebidos no registro:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id', // Validar que o user_id existe
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients',
            'celphone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string|max:255',
            'number' => 'required|string|max:10',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'zipcode' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            Log::error('Erros de validação:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $client = Client::create([
                'user_id' => $request->user_id, // ID da loja
                'name' => $request->name,
                'email' => $request->email,
                'celphone' => $request->celphone,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                'number' => $request->number,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'zipcode' => $request->zipcode,
                'reference_point' => $request->reference_point,
                'instagram' => $request->instagram,
                'facebook' => $request->facebook,
                'site' => $request->site,
            ]);

            Auth::guard('client')->login($client);

            return response()->json([
                'success' => true,
                'message' => 'Conta criada com sucesso!',
                'client' => $client
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao criar cliente:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor. Tente novamente.'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso!'
        ]);
    }

    public function user()
    {
        return response()->json([
            'success' => true,
            'client' => Auth::guard('client')->user()
        ]);
    }
}