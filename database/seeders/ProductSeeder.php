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
        $user = User::first();
        $categories = Category::all();

        if (!$user || $categories->isEmpty()) {
            $this->command->error('É necessário ter pelo menos um usuário e categorias antes de executar este seeder.');
            return;
        }

        $products = [
            // Eletrônicos
            [
                'category_name' => 'Eletrônicos',
                'products' => [
                    [
                        'bar_code' => '7891234567890',
                        'name' => 'Smartphone Samsung Galaxy A54',
                        'description' => 'Smartphone com tela de 6.4", 128GB de armazenamento, câmera tripla de 50MP e bateria de 5000mAh.',
                        'price' => 1299.99,
                        'stock' => 25,
                        'images' => ['samsung-a54-1.jpg', 'samsung-a54-2.jpg'],
                    ],
                    [
                        'bar_code' => '7891234567891',
                        'name' => 'Notebook Dell Inspiron 15',
                        'description' => 'Notebook com processador Intel i5, 8GB RAM, SSD 256GB, tela 15.6" Full HD.',
                        'price' => 2499.99,
                        'stock' => 15,
                        'images' => ['dell-inspiron-1.jpg', 'dell-inspiron-2.jpg'],
                    ],
                    [
                        'bar_code' => '7891234567892',
                        'name' => 'Fone de Ouvido Bluetooth JBL',
                        'description' => 'Fone de ouvido sem fio com cancelamento de ruído e bateria de 30 horas.',
                        'price' => 299.99,
                        'stock' => 50,
                        'images' => ['jbl-headphone-1.jpg'],
                    ],
                ],
            ],
            // Roupas e Acessórios
            [
                'category_name' => 'Roupas e Acessórios',
                'products' => [
                    [
                        'bar_code' => '7891234567893',
                        'name' => 'Camiseta Básica Algodão',
                        'description' => 'Camiseta 100% algodão, disponível em várias cores e tamanhos.',
                        'price' => 39.99,
                        'stock' => 100,
                        'images' => ['camiseta-basica-1.jpg', 'camiseta-basica-2.jpg'],
                    ],
                    [
                        'bar_code' => '7891234567894',
                        'name' => 'Tênis Esportivo Nike',
                        'description' => 'Tênis para corrida com tecnologia Air Max e solado antiderrapante.',
                        'price' => 399.99,
                        'stock' => 30,
                        'images' => ['nike-tenis-1.jpg', 'nike-tenis-2.jpg'],
                    ],
                    [
                        'bar_code' => '7891234567895',
                        'name' => 'Relógio Digital Casio',
                        'description' => 'Relógio digital resistente à água com cronômetro e alarme.',
                        'price' => 149.99,
                        'stock' => 40,
                        'images' => ['casio-watch-1.jpg'],
                    ],
                ],
            ],
            // Casa e Jardim
            [
                'category_name' => 'Casa e Jardim',
                'products' => [
                    [
                        'bar_code' => '7891234567896',
                        'name' => 'Conjunto de Panelas Antiaderentes',
                        'description' => 'Kit com 5 panelas antiaderentes com tampas de vidro e cabos ergonômicos.',
                        'price' => 199.99,
                        'stock' => 20,
                        'images' => ['panelas-kit-1.jpg', 'panelas-kit-2.jpg'],
                    ],
                    [
                        'bar_code' => '7891234567897',
                        'name' => 'Aspirador de Pó Vertical',
                        'description' => 'Aspirador sem fio com bateria recarregável e filtro HEPA.',
                        'price' => 599.99,
                        'stock' => 12,
                        'images' => ['aspirador-1.jpg'],
                    ],
                ],
            ],
            // Livros e Mídia
            [
                'category_name' => 'Livros e Mídia',
                'products' => [
                    [
                        'bar_code' => '9788535902777',
                        'name' => 'O Alquimista - Paulo Coelho',
                        'description' => 'Romance sobre a jornada de um jovem pastor em busca de seu tesouro pessoal.',
                        'price' => 29.99,
                        'stock' => 60,
                        'images' => ['alquimista-1.jpg'],
                    ],
                    [
                        'bar_code' => '9788547416485',
                        'name' => 'Clean Code - Robert Martin',
                        'description' => 'Guia prático para escrever código limpo e manutenível.',
                        'price' => 89.99,
                        'stock' => 35,
                        'images' => ['clean-code-1.jpg'],
                    ],
                ],
            ],
            // Esportes e Lazer
            [
                'category_name' => 'Esportes e Lazer',
                'products' => [
                    [
                        'bar_code' => '7891234567898',
                        'name' => 'Bicicleta Mountain Bike Aro 29',
                        'description' => 'Bicicleta com quadro de alumínio, 21 marchas e freios a disco.',
                        'price' => 1299.99,
                        'stock' => 8,
                        'images' => ['bike-mtb-1.jpg', 'bike-mtb-2.jpg'],
                    ],
                    [
                        'bar_code' => '7891234567899',
                        'name' => 'Kit Halteres Ajustáveis',
                        'description' => 'Par de halteres com pesos ajustáveis de 2kg a 20kg cada.',
                        'price' => 299.99,
                        'stock' => 15,
                        'images' => ['halteres-1.jpg'],
                    ],
                ],
            ],
            // Beleza e Cuidados Pessoais
            [
                'category_name' => 'Beleza e Cuidados Pessoais',
                'products' => [
                    [
                        'bar_code' => '7891234567900',
                        'name' => 'Kit Shampoo e Condicionador',
                        'description' => 'Kit para cabelos oleosos com extratos naturais, 400ml cada.',
                        'price' => 49.99,
                        'stock' => 80,
                        'images' => ['shampoo-kit-1.jpg'],
                    ],
                    [
                        'bar_code' => '7891234567901',
                        'name' => 'Perfume Masculino 100ml',
                        'description' => 'Fragrância amadeirada com notas de sândalo e cedro.',
                        'price' => 159.99,
                        'stock' => 25,
                        'images' => ['perfume-masc-1.jpg'],
                    ],
                ],
            ],
        ];

        foreach ($products as $categoryProducts) {
            $category = $categories->where('name', $categoryProducts['category_name'])->first();
            
            if ($category) {
                foreach ($categoryProducts['products'] as $productData) {
                    Product::create([
                        'user_id' => $user->id,
                        'category_id' => $category->id,
                        'bar_code' => $productData['bar_code'],
                        'name' => $productData['name'],
                        'description' => $productData['description'],
                        'price' => $productData['price'],
                        'stock' => $productData['stock'],
                        'images' => $productData['images'],
                    ]);
                }
            }
        }
    }
}