<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cadastro - Cliente</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-6 flex flex-col justify-center sm:py-12">
        <div class="relative py-3 sm:max-w-xl sm:mx-auto">
            <div class="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
                <div class="max-w-md mx-auto">
                    <h2 class="text-2xl font-bold text-center text-gray-900 mb-6">Criar Conta</h2>
                    
                    <form id="registerForm" class="space-y-4">
                        <input type="hidden" name="user_id" id="user_id" value="{{ request('store_id', 2) }}">
                        
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nome Completo</label>
                            <input type="text" id="name" name="name" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                            <input type="email" id="email" name="email" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="celphone" class="block text-sm font-medium text-gray-700">Telefone</label>
                            <input type="text" id="celphone" name="celphone" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                            <input type="password" id="password" name="password" required minlength="8"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Endereço</label>
                            <input type="text" id="address" name="address" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="number" class="block text-sm font-medium text-gray-700">Número</label>
                                <input type="text" id="number" name="number" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="zipcode" class="block text-sm font-medium text-gray-700">CEP</label>
                                <input type="text" id="zipcode" name="zipcode" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">Cidade</label>
                            <input type="text" id="city" name="city" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="state" class="block text-sm font-medium text-gray-700">Estado</label>
                                <input type="text" id="state" name="state" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700">País</label>
                                <input type="text" id="country" name="country" value="Brasil" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div>
                            <label for="reference_point" class="block text-sm font-medium text-gray-700">Ponto de Referência (opcional)</label>
                            <input type="text" id="reference_point" name="reference_point"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <button type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Criar Conta
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600">
                            Já tem uma conta?
                            <a href="{{ route('client.login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                Faça login
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Verificar se o store_id foi passado via URL
            const urlParams = new URLSearchParams(window.location.search);
            const storeId = urlParams.get('store_id');
            
            if (storeId) {
                document.getElementById('user_id').value = storeId;
                console.log('Store ID definido via URL:', storeId);
            } else {
                // Se não tiver store_id na URL, usar o valor padrão (ID 2 da TechStore Brasil)
                const defaultStoreId = document.getElementById('user_id').value;
                console.log('Usando Store ID padrão:', defaultStoreId);
            }

            document.getElementById('registerForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Verificar se o user_id foi definido
                const userId = document.getElementById('user_id').value;
                console.log('User ID no envio:', userId);
                
                if (!userId || userId === 'null' || userId === '') {
                    alert('Erro: ID da loja não encontrado. Tente acessar o cadastro através da página da loja.');
                    return;
                }
                
                const formData = new FormData(this);
                
                // Debug: mostrar todos os dados do formulário
                console.log('Dados do formulário:');
                for (let [key, value] of formData.entries()) {
                    console.log(key, value);
                }
                
                try {
                    const response = await fetch('{{ route("client.register.post") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        alert(data.message);
                        // Redirecionar para a página anterior ou home
                        const returnUrl = urlParams.get('return') || '/';
                        window.location.href = returnUrl;
                    } else {
                        if (data.errors) {
                            let errorMessage = 'Erros encontrados:\n';
                            for (const field in data.errors) {
                                errorMessage += `${field}: ${data.errors[field].join(', ')}\n`;
                            }
                            alert(errorMessage);
                        } else {
                            alert(data.message);
                        }
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    alert('Erro ao criar conta. Tente novamente.');
                }
            });
        });
    </script>
</body>
</html>