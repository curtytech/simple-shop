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
                        'images' => [
                            'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=400&fit=crop',
                            'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400&h=400&fit=crop'
                        ],
                    ],
                    [
                        'bar_code' => '7891234567891',
                        'name' => 'Notebook Dell Inspiron 15',
                        'description' => 'Notebook com processador Intel i5, 8GB RAM, SSD 256GB, tela 15.6" Full HD.',
                        'price' => 2499.99,
                        'stock' => 15,
                        'images' => [
                            'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=400&fit=crop',
                            'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=400&h=400&fit=crop'
                        ],
                    ],
                    [
                        'bar_code' => '7891234567892',
                        'name' => 'Fone de Ouvido Bluetooth JBL',
                        'description' => 'Fone de ouvido sem fio com cancelamento de ruído e bateria de 30 horas.',
                        'price' => 299.99,
                        'stock' => 50,
                        'images' => [
                            'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop'
                        ],
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
                        'images' => [
                            'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&h=400&fit=crop',
                            'https://images.unsplash.com/photo-1583743814966-8936f37f4678?w=400&h=400&fit=crop'
                        ],
                    ],
                    [
                        'bar_code' => '7891234567894',
                        'name' => 'Tênis Esportivo Nike',
                        'description' => 'Tênis para corrida com tecnologia Air Max e solado antiderrapante.',
                        'price' => 399.99,
                        'stock' => 30,
                        'images' => [
                            'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop',
                            'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=400&h=400&fit=crop'
                        ],
                    ],
                    [
                        'bar_code' => '7891234567895',
                        'name' => 'Relógio Digital Casio',
                        'description' => 'Relógio digital resistente à água com cronômetro e alarme.',
                        'price' => 149.99,
                        'stock' => 40,
                        'images' => [
                            'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop'
                        ],
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
                        'images' => [
                            'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=400&h=400&fit=crop',
                            'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=400&h=400&fit=crop'
                        ],
                    ],
                    [
                        'bar_code' => '7891234567897',
                        'name' => 'Aspirador de Pó Vertical',
                        'description' => 'Aspirador sem fio com bateria recarregável e filtro HEPA.',
                        'price' => 599.99,
                        'stock' => 12,
                        'images' => [
                            'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=400&fit=crop'
                        ],
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
                        'images' => [
                            'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=400&h=400&fit=crop'
                        ],
                    ],
                    [
                        'bar_code' => '9788547416485',
                        'name' => 'Clean Code - Robert Martin',
                        'description' => 'Guia prático para escrever código limpo e manutenível.',
                        'price' => 89.99,
                        'stock' => 35,
                        'images' => [
                            'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=400&h=400&fit=crop'
                        ],
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
                        'images' => [
                            'https://images.unsplash.com/photo-1544191696-15693072e0b5?w=400&h=400&fit=crop',
                            'https://images.unsplash.com/photo-1571068316344-75bc76f77890?w=400&h=400&fit=crop'
                        ],
                    ],
                    [
                        'bar_code' => '7891234567899',
                        'name' => 'Kit Halteres Ajustáveis',
                        'description' => 'Par de halteres com pesos ajustáveis de 2kg a 20kg cada.',
                        'price' => 299.99,
                        'stock' => 15,
                        'images' => [
                            'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=400&fit=crop'
                        ],
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
                        'images' => [
                            'https://images.unsplash.com/photo-1556228578-8c89e6adf883?w=400&h=400&fit=crop'
                        ],
                    ],
                    [
                        'bar_code' => '7891234567901',
                        'name' => 'Perfume Masculino 100ml',
                        'description' => 'Fragrância amadeirada com notas de sândalo e cedro.',
                        'price' => 159.99,
                        'stock' => 25,
                        'images' => [
                            'https://images.unsplash.com/photo-1541643600914-78b084683601?w=400&h=400&fit=crop'
                        ],
                    ],
                ],
            ],
            // Alimentação
            [
                'category_name' => 'Alimentação',
                'products' => [
                    [
                        'bar_code' => '7891234567902',
                        'name' => 'Café Premium Torrado e Moído',
                        'description' => 'Café especial 100% arábica, torra média, pacote de 500g.',
                        'price' => 24.99,
                        'stock' => 120,
                        'images' => [
                            'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=400&h=400&fit=crop'
                        ],
                    ],
                    [
                        'bar_code' => '7891234567903',
                        'name' => 'Azeite Extra Virgem 500ml',
                        'description' => 'Azeite de oliva extra virgem português, primeira prensagem a frio.',
                        'price' => 45.99,
                        'stock' => 60,
                        'images' => [
                            'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400&h=400&fit=crop'
                        ],
                    ],
                ],
            ],
            // Brinquedos e Jogos
            [
                'category_name' => 'Brinquedos e Jogos',
                'products' => [
                    [
                        'bar_code' => '7891234567904',
                        'name' => 'Quebra-cabeça 1000 peças',
                        'description' => 'Quebra-cabeça com imagem de paisagem natural, 1000 peças.',
                        'price' => 39.99,
                        'stock' => 45,
                        'images' => [
                            'https://images.unsplash.com/photo-1606092195730-5d7b9af1efc5?w=400&h=400&fit=crop'
                        ],
                    ],
                    [
                        'bar_code' => '7891234567905',
                        'name' => 'Boneca Articulada 30cm',
                        'description' => 'Boneca com articulações móveis, cabelos longos e roupas removíveis.',
                        'price' => 79.99,
                        'stock' => 30,
                        'images' => [
                            'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=400&fit=crop'
                        ],
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