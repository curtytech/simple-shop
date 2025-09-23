<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Busca o usuário da loja TechStore Brasil
        $storeUser = User::where('email', 'user@user')->first();
        
        if (!$storeUser) {
            return;
        }

        // Busca as categorias da TechStore Brasil
        $smartphonesCategory = Category::where('name', 'Smartphones')->where('user_id', $storeUser->id)->first();
        $notebooksCategory = Category::where('name', 'Notebooks e Computadores')->where('user_id', $storeUser->id)->first();
        $acessoriosCategory = Category::where('name', 'Acessórios Tech')->where('user_id', $storeUser->id)->first();
        $gamingCategory = Category::where('name', 'Gaming')->where('user_id', $storeUser->id)->first();
        $smartHomeCategory = Category::where('name', 'Smart Home')->where('user_id', $storeUser->id)->first();

        $products = [];

        // Produtos de Smartphones
        if ($smartphonesCategory) {
            $products = array_merge($products, [
                [
                    'user_id' => $storeUser->id,
                    'category_id' => $smartphonesCategory->id,
                    'bar_code' => '7891234567890',
                    'name' => 'iPhone 15 Pro Max 256GB',
                    'description' => 'O mais avançado iPhone com chip A17 Pro, câmera profissional e tela Super Retina XDR de 6.7 polegadas.',
                    'price' => 8999.00,
                    'stock' => 15,
                    'images' => [
                        'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400&h=400&fit=crop',
                        'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=400&fit=crop'
                    ],
                ],
                [
                    'user_id' => $storeUser->id,
                    'category_id' => $smartphonesCategory->id,
                    'bar_code' => '7891234567891',
                    'name' => 'Samsung Galaxy S24 Ultra 512GB',
                    'description' => 'Smartphone premium com S Pen integrada, câmera de 200MP e tela Dynamic AMOLED 2X de 6.8 polegadas.',
                    'price' => 7499.00,
                    'stock' => 12,
                    'images' => [
                        'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=400&h=400&fit=crop',
                        'https://images.unsplash.com/photo-1580910051074-3eb694886505?w=400&h=400&fit=crop'
                    ],
                ],
                [
                    'user_id' => $storeUser->id,
                    'category_id' => $smartphonesCategory->id,
                    'bar_code' => '7891234567892',
                    'name' => 'Xiaomi 14 Ultra 256GB',
                    'description' => 'Smartphone com câmera Leica, processador Snapdragon 8 Gen 3 e carregamento rápido de 90W.',
                    'price' => 4299.00,
                    'stock' => 20,
                    'images' => [
                        'https://images.unsplash.com/photo-1567581935884-3349723552ca?w=400&h=400&fit=crop'
                    ],
                ],
            ]);
        }

        // Produtos de Notebooks e Computadores
        if ($notebooksCategory) {
            $products = array_merge($products, [
                [
                    'user_id' => $storeUser->id,
                    'category_id' => $notebooksCategory->id,
                    'bar_code' => '7891234567893',
                    'name' => 'MacBook Pro 16" M3 Max 1TB',
                    'description' => 'Notebook profissional com chip M3 Max, 36GB de RAM e tela Liquid Retina XDR de 16 polegadas.',
                    'price' => 24999.00,
                    'stock' => 5,
                    'images' => [
                        'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400&h=400&fit=crop',
                        'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=400&fit=crop'
                    ],
                ],
                [
                    'user_id' => $storeUser->id,
                    'category_id' => $notebooksCategory->id,
                    'bar_code' => '7891234567894',
                    'name' => 'Dell XPS 15 OLED i9 32GB 1TB RTX 4070',
                    'description' => 'Notebook premium com tela OLED 4K, processador Intel i9 e placa de vídeo RTX 4070.',
                    'price' => 18999.00,
                    'stock' => 8,
                    'images' => [
                        'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=400&h=400&fit=crop'
                    ],
                ],
                [
                    'user_id' => $storeUser->id,
                    'category_id' => $notebooksCategory->id,
                    'bar_code' => '7891234567895',
                    'name' => 'ASUS ROG Strix G16 RTX 4060',
                    'description' => 'Notebook gamer com Intel i7, 16GB RAM, SSD 512GB e placa de vídeo RTX 4060.',
                    'price' => 7999.00,
                    'stock' => 10,
                    'images' => [
                        'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=400&h=400&fit=crop'
                    ],
                ],
            ]);
        }

        // Produtos de Acessórios Tech
        if ($acessoriosCategory) {
            $products = array_merge($products, [
                [
                    'user_id' => $storeUser->id,
                    'category_id' => $acessoriosCategory->id,
                    'bar_code' => '7891234567896',
                    'name' => 'AirPods Pro 2ª Geração',
                    'description' => 'Fones de ouvido sem fio com cancelamento ativo de ruído e áudio espacial.',
                    'price' => 2299.00,
                    'stock' => 25,
                    'images' => [
                        'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=400&h=400&fit=crop'
                    ],
                ],
                [
                    'user_id' => $storeUser->id,
                    'category_id' => $acessoriosCategory->id,
                    'bar_code' => '7891234567897',
                    'name' => 'Carregador Sem Fio MagSafe 15W',
                    'description' => 'Carregador sem fio magnético compatível com iPhone e AirPods.',
                    'price' => 399.00,
                    'stock' => 30,
                    'images' => [
                        'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=400&h=400&fit=crop'
                    ],
                ],
                [
                    'user_id' => $storeUser->id,
                    'category_id' => $acessoriosCategory->id,
                    'bar_code' => '7891234567898',
                    'name' => 'Webcam Logitech C920 HD Pro',
                    'description' => 'Webcam Full HD 1080p com microfone estéreo integrado para videoconferências.',
                    'price' => 599.00,
                    'stock' => 18,
                    'images' => [
                        'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=400&h=400&fit=crop'
                    ],
                ],
            ]);
        }

        // Produtos de Gaming
        if ($gamingCategory) {
            $products = array_merge($products, [
                [
                    'user_id' => $storeUser->id,
                    'category_id' => $gamingCategory->id,
                    'bar_code' => '7891234567899',
                    'name' => 'PlayStation 5 Slim 1TB',
                    'description' => 'Console de nova geração com SSD ultra-rápido e gráficos 4K.',
                    'price' => 4199.00,
                    'stock' => 6,
                    'images' => [
                        'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?w=400&h=400&fit=crop'
                    ],
                ],
                [
                    'user_id' => $storeUser->id,
                    'category_id' => $gamingCategory->id,
                    'bar_code' => '7891234567900',
                    'name' => 'Controle Xbox Wireless Elite Series 2',
                    'description' => 'Controle premium com paddles ajustáveis e gatilhos de cabelo.',
                    'price' => 1299.00,
                    'stock' => 15,
                    'images' => [
                        'https://images.unsplash.com/photo-1493711662062-fa541adb3fc8?w=400&h=400&fit=crop'
                    ],
                ],
            ]);
        }

        // Produtos de Smart Home
        if ($smartHomeCategory) {
            $products = array_merge($products, [
                [
                    'user_id' => $storeUser->id,
                    'category_id' => $smartHomeCategory->id,
                    'bar_code' => '7891234567901',
                    'name' => 'Echo Dot 5ª Geração com Alexa',
                    'description' => 'Alto-falante inteligente com Alexa, som melhorado e hub integrado.',
                    'price' => 349.00,
                    'stock' => 22,
                    'images' => [
                        'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=400&fit=crop'
                    ],
                ],
                [
                    'user_id' => $storeUser->id,
                    'category_id' => $smartHomeCategory->id,
                    'bar_code' => '7891234567902',
                    'name' => 'Lâmpada Inteligente Philips Hue',
                    'description' => 'Lâmpada LED inteligente com 16 milhões de cores e controle por app.',
                    'price' => 199.00,
                    'stock' => 35,
                    'images' => [
                        'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=400&fit=crop'
                    ],
                ],
            ]);
        }

        // Criar todos os produtos
        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}