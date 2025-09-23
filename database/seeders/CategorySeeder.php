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
            // Categorias da TechStore Brasil (store user)
            [
                'user_id' => $storeUser->id,
                'name' => 'Smartphones',
                'description' => 'Smartphones das principais marcas com as melhores tecnologias.',
                'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=400&fit=crop',
            ],
            [
                'user_id' => $storeUser->id,
                'name' => 'Notebooks e Computadores',
                'description' => 'Notebooks, desktops e workstations para trabalho e gaming.',
                'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=400&fit=crop',
            ],
            [
                'user_id' => $storeUser->id,
                'name' => 'Acessórios Tech',
                'description' => 'Fones de ouvido, carregadores, capas e outros acessórios tecnológicos.',
                'image' => 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=400&h=400&fit=crop',
            ],
            [
                'user_id' => $storeUser->id,
                'name' => 'Gaming',
                'description' => 'Consoles, jogos, periféricos e acessórios para gamers.',
                'image' => 'https://images.unsplash.com/photo-1493711662062-fa541adb3fc8?w=400&h=400&fit=crop',
            ],
            [
                'user_id' => $storeUser->id,
                'name' => 'Smart Home',
                'description' => 'Dispositivos inteligentes para automação residencial.',
                'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=400&fit=crop',
            ],
            
            // Categorias do Admin (outras lojas)
            [
                'user_id' => $adminUser->id,
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
                'user_id' => $adminUser->id,
                'name' => 'Beleza e Cuidados Pessoais',
                'description' => 'Cosméticos, produtos de higiene pessoal e cuidados com a pele.',
                'image' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=400&h=400&fit=crop',
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }
    }
}