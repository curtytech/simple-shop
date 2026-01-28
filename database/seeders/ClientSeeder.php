<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Busca os usuários criados na migration        

        $clients = [
            // Categorias da TechStore Brasil (store user)
            [
                'user_id' => 2,
                'name' => 'Phelipe Curty Client',
                'email' => 'phelipecurty@gmail.com',
                'password' => bcrypt('12345678'),
                'celphone' => '12345678901',
                'address' => 'Rua Exemplo, 123',
                'city' => 'Cidade Exemplo',
                'state' => 'Estado Exemplo',
                'country' => 'País Exemplo',
                'zipcode' => '12345678',
                // 'verify_email' => 1,
            ],
           
        ];

        foreach ($clients as $clientData) {
            Client::create($clientData);
        }
    }
}