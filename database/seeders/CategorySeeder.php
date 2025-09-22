<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Busca o primeiro usuário ou cria um se não existir
        $user = User::first();
        
        if (!$user) {
            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $categories = [
            [
                'name' => 'Eletrônicos',
                'description' => 'Produtos eletrônicos como smartphones, tablets, notebooks e acessórios.',
                'image' => 'electronics.jpg',
            ],
            [
                'name' => 'Roupas e Acessórios',
                'description' => 'Vestuário masculino e feminino, calçados e acessórios de moda.',
                'image' => 'clothing.jpg',
            ],
            [
                'name' => 'Casa e Jardim',
                'description' => 'Produtos para decoração, móveis, utensílios domésticos e jardinagem.',
                'image' => 'home-garden.jpg',
            ],
            [
                'name' => 'Livros e Mídia',
                'description' => 'Livros, revistas, filmes, música e produtos de entretenimento.',
                'image' => 'books-media.jpg',
            ],
            [
                'name' => 'Esportes e Lazer',
                'description' => 'Equipamentos esportivos, roupas de ginástica e produtos para atividades ao ar livre.',
                'image' => 'sports.jpg',
            ],
            [
                'name' => 'Beleza e Cuidados Pessoais',
                'description' => 'Cosméticos, produtos de higiene pessoal e cuidados com a pele.',
                'image' => 'beauty.jpg',
            ],
            [
                'name' => 'Alimentação',
                'description' => 'Alimentos, bebidas, suplementos e produtos gourmet.',
                'image' => 'food.jpg',
            ],
            [
                'name' => 'Brinquedos e Jogos',
                'description' => 'Brinquedos infantis, jogos de tabuleiro e produtos educativos.',
                'image' => 'toys.jpg',
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::create([
                'user_id' => $user->id,
                'name' => $categoryData['name'],
                'description' => $categoryData['description'],
                'image' => $categoryData['image'],
            ]);
        }
    }
}