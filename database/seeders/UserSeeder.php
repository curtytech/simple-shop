<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Busca os usuários criados na migration        

        $users = [
            // Categorias da TechStore Brasil (store user)
            [
                'celphone' => '21986342478',
                'address' => 'Rua Exemplo, 123',
                'city' => 'Cidade Exemplo',
                'state' => 'Estado Exemplo',
                'country' => 'País Exemplo',
                'zipcode' => '25908800',
                'mercadopago_environment' => 'sandbox',
                'mercadopago_sandbox_public_key' => 'APP_USR-363f0832-00ac-45ef-9be2-09cd05088f45',
                'mercadopago_sandbox_access_token' => 'APP_USR-7300805590293882-102309-942806e25252e2ab4eeefb6f17bd74d2-2653728447',
                'mercadopago_sandbox_webhook_url' => 'https://shop.you.tec.br',
                'mercadopago_sandbox_webhook_secret' => '1b72ab987b7828101db2c6a88336efbc10c92f8ae3e2f5fccac0375c3356f52b',
            ],
           
        ];

        foreach ($users as $userData) {
            User::whereKey(2)->update($userData);
        }
    }
}
