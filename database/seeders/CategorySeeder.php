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
        // Busca os usuários criados na migration
        $adminUser = User::where('email', 'admin@admin')->first();
        $storeUser = User::where('email', 'user@user')->first();

        $categories = [
            [
                'user_id' => $adminUser->id,
                'name' => 'Eletrônicos',
                'description' => 'Produtos eletrônicos como smartphones, tablets, notebooks e acessórios.',
                'image' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=400&h=400&fit=crop',
            ],
            [
                'user_id' => $storeUser->id,
                'name' => 'Roupas e Acessórios',
                'description' => 'Vestuário masculino e feminino, calçados e acessórios de moda.',
                'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=400&fit=crop',
            ],
            [
                'user_id' => $adminUser->id,
                'name' => 'Casa e Jardim',
                'description' => 'Produtos para decoração, móveis, utensílios domésticos e jardinagem.',
                'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&h=400&fit=crop',
            ],
            [
                'user_id' => $storeUser->id,
                'name' => 'Livros e Mídia',
                'description' => 'Livros, revistas, filmes, música e produtos de entretenimento.',
                'image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=400&h=400&fit=crop',
            ],
            [
                'user_id' => $adminUser->id,
                'name' => 'Esportes e Lazer',
                'description' => 'Equipamentos esportivos, roupas de ginástica e produtos para atividades ao ar livre.',
                'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=400&fit=crop',
            ],
            [
                'user_id' => $storeUser->id,
                'name' => 'Beleza e Cuidados Pessoais',
                'description' => 'Cosméticos, produtos de higiene pessoal e cuidados com a pele.',
                'image' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=400&h=400&fit=crop',
            ],
            [
                'user_id' => $adminUser->id,
                'name' => 'Alimentação',
                'description' => 'Alimentos, bebidas, suplementos e produtos gourmet.',
                'image' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&h=400&fit=crop',
            ],
            [
                'user_id' => $storeUser->id,
                'name' => 'Brinquedos e Jogos',
                'description' => 'Brinquedos infantis, jogos de tabuleiro e produtos educativos.',
                'image' => 'https://images.unsplash.com/photo-1558060370-d644479cb6f7?w=400&h=400&fit=crop',
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }
    }
}