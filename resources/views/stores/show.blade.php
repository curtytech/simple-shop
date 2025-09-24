<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $store->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-50">
    <!-- Header com Banner -->
    <div class="relative h-64 bg-gradient-to-r from-blue-500 to-purple-600 overflow-hidden">
        @if($store->banner && (str_starts_with($store->banner, 'http://') || str_starts_with($store->banner, 'https://')))
        <img src="{{ $store->banner }}" alt="Banner {{ $store->name }}" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        @endif

        <!-- Logo e Informações -->
        <div class="absolute bottom-0 left-0 right-0 p-6">
            <div class="container mx-auto flex items-end space-x-4">
                <!-- Logo -->
                <div class="w-24 h-24 bg-white rounded-full p-2 shadow-lg">
                    @if($store->logo && (str_starts_with($store->logo, 'http://') || str_starts_with($store->logo, 'https://')))
                    <img src="{{ $store->logo }}" alt="Logo {{ $store->name }}" class="w-full h-full object-cover rounded-full">
                    @else
                    <div class="w-full h-full bg-gray-200 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    @endif
                </div>

                <!-- Informações da Loja -->
                <div class="text-white">
                    <h1 class="text-3xl font-bold">{{ $store->name }}</h1>
                    @if($store->slogan)
                    <p class="text-lg opacity-90">{{ $store->slogan }}</p>
                    @endif
                    @if($store->celphone)
                    <p class="text-sm opacity-75">📞 {{ $store->celphone }}</p>
                    @endif
                </div>

                <!-- Área de Login/Carrinho -->
                <div class="ml-auto flex items-center space-x-4">
                    <!-- Status do Cliente -->
                    <div id="client-status" class="text-white text-sm">
                        <span id="client-name" class="hidden"></span>
                        <button id="logout-btn" class="hidden text-red-300 hover:text-red-100 ml-2">Sair</button>
                    </div>

                    <!-- Botão de Login -->
                    <button id="login-btn" class="bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-green-600 transition-colors">
                        Entrar
                    </button>

                    <!-- Carrinho -->
                    <button id="cart-btn" class="bg-white text-gray-800 px-4 py-2 rounded-lg shadow-lg hover:bg-gray-100 transition-colors flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path>
                        </svg>
                        <span>Carrinho</span>
                        <span id="cart-count" class="bg-red-500 text-white text-xs px-2 py-1 rounded-full hidden">0</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Informações da Loja -->
    <div class="container mx-auto px-4 mt-20">
        <!-- Filtros de Categoria -->
        @if($store->categories->count() > 0)
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Categorias</h2>
            <div class="flex flex-wrap gap-2 mb-6">
                <button class="category-filter active px-4 py-2 rounded-full bg-blue-500 text-white hover:bg-blue-600 transition-colors" data-category="all">
                    Todas
                </button>
                @foreach($store->categories as $category)
                <button class="category-filter px-4 py-2 rounded-full bg-gray-200 text-gray-700 hover:bg-blue-500 hover:text-white transition-colors" data-category="{{ $category->id }}">
                    {{ $category->name }}
                </button>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Produtos -->
        @if($store->products->count() > 0)
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Produtos</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" id="products-grid">
                @foreach($store->products as $product)
                <div class="product-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow" data-category="{{ $product->category_id }}">
                    <!-- Imagem do Produto -->
                    <div class="w-full h-48 bg-gray-200">
                        @if($product->images && count($product->images) > 0)
                        <img src="{{ $product->images[0] }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        @endif
                    </div>

                    <!-- Informações do Produto -->
                    <div class="p-4">
                        <h3 class="font-semibold text-lg text-gray-800 mb-2">{{ $product->name }}</h3>
                        @if($product->description)
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $product->description }}</p>
                        @endif

                        <!-- Preço -->
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-2xl font-bold text-green-600">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                            @if($product->stock > 0)
                            <span class="text-sm text-gray-500">{{ $product->stock }} em estoque</span>
                            @else
                            <span class="text-sm text-red-500">Sem estoque</span>
                            @endif
                        </div>

                        <!-- Controles de Quantidade e Adicionar ao Carrinho -->
                        @if($product->stock > 0)
                        <div class="flex items-center space-x-2">
                            <div class="flex items-center border rounded-lg">
                                <button class="quantity-btn minus px-3 py-1 text-gray-600 hover:bg-gray-100" data-product="{{ $product->id }}">-</button>
                                <input type="number" class="quantity-input w-16 text-center border-0 focus:ring-0" value="1" min="1" max="{{ $product->stock }}" data-product="{{ $product->id }}">
                                <button class="quantity-btn plus px-3 py-1 text-gray-600 hover:bg-gray-100" data-product="{{ $product->id }}">+</button>
                            </div>
                            <button class="add-to-cart flex-1 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors" data-product="{{ $product->id }}">
                                Adicionar
                            </button>
                        </div>
                        @else
                        <button class="w-full bg-gray-300 text-gray-500 px-4 py-2 rounded-lg cursor-not-allowed" disabled>
                            Indisponível
                        </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-8">Nenhum produto disponível no momento.</p>
            @endif
        </div>
    </div>

    <!-- Modal de Login/Registro -->
    <div id="auth-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Faça login para continuar</h3>
                    <button id="close-auth-modal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-gray-600 mb-6">Você precisa ter uma conta para adicionar produtos ao carrinho.</p>
                <div class="space-y-3">
                    <a href="{{ route('client.login') }}?return={{ urlencode(request()->fullUrl()) }}"
                        class="block w-full bg-blue-500 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-600 transition-colors">
                        Fazer Login
                    </a>
                    <a href="{{ route('client.register') }}?store_id={{ $store->id }}&return={{ urlencode(request()->fullUrl()) }}"
                        class="block w-full bg-green-500 text-white text-center py-2 px-4 rounded-lg hover:bg-green-600 transition-colors">
                        Criar Conta
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal do Carrinho -->
    <div id="cart-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full max-h-[80vh] overflow-hidden">
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-lg font-semibold">Meu Carrinho</h3>
                    <button id="close-cart-modal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div id="cart-content" class="p-6 overflow-y-auto max-h-96">
                    <!-- Conteúdo do carrinho será carregado aqui -->
                </div>

                <div class="border-t p-6">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-lg font-semibold">Total:</span>
                        <span id="cart-total" class="text-2xl font-bold text-green-600">R$ 0,00</span>
                    </div>
                    <div class="flex space-x-3">
                        <button id="clear-cart" class="flex-1 bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition-colors">
                            Limpar Carrinho
                        </button>
                        <button id="checkout" class="flex-1 bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition-colors">
                            Finalizar Compra
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rodapé -->
    <footer class="bg-gray-800 text-white mt-16">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Informações da Loja -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">{{ $store->name }}</h3>
                    @if($store->slogan)
                    <p class="text-gray-300 mb-2">{{ $store->slogan }}</p>
                    @endif
                    @if($store->celphone)
                    <p class="text-gray-300">📞 {{ $store->celphone }}</p>
                    @endif
                </div>

                <!-- Links Rápidos -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Links Rápidos</h3>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="#" class="hover:text-white">Sobre Nós</a></li>
                        <li><a href="#" class="hover:text-white">Contato</a></li>
                        <li><a href="#" class="hover:text-white">Política de Privacidade</a></li>
                        <li><a href="#" class="hover:text-white">Termos de Uso</a></li>
                    </ul>
                </div>

                <!-- Redes Sociais -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Siga-nos</h3>
                    <div class="flex space-x-4">
                        @if($store->facebook)
                        <a href="{{ $store->facebook }}" target="_blank" class="text-gray-300 hover:text-white">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        @endif
                        @if($store->instagram)
                        <a href="{{ $store->instagram }}" target="_blank" class="text-gray-300 hover:text-white">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297C4.198 14.895 3.708 13.744 3.708 12.447s.49-2.448 1.297-3.323c.875-.807 2.026-1.297 3.323-1.297s2.448.49 3.323 1.297c.807.875 1.297 2.026 1.297 3.323s-.49 2.448-1.297 3.323c-.875.807-2.026 1.297-3.323 1.297z" />
                            </svg>
                        </a>
                        @endif
                        @if($store->site)
                        <a href="{{ $store->site }}" target="_blank" class="text-gray-300 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Variáveis globais
        let currentClient = null;
        const storeId = @json($store->id);

        document.addEventListener('DOMContentLoaded', function() {
            // Verificar autenticação do cliente
            checkClientAuth();

            // Event listeners
            setupEventListeners();
        });

        function setupEventListeners() {
            // Botão de login
            document.getElementById('login-btn').addEventListener('click', function() {
                window.location.href = '{{ route("client.login") }}?return=' + encodeURIComponent(window.location.href);
            });

            // Botão de logout
            document.getElementById('logout-btn').addEventListener('click', logout);

            // Botão do carrinho
            document.getElementById('cart-btn').addEventListener('click', function() {
                if (!currentClient) {
                    showAuthModal();
                    return;
                }
                document.getElementById('cart-modal').classList.remove('hidden');
                loadCart();
            });

            // Limpar carrinho
            document.getElementById('clear-cart').addEventListener('click', async function() {
                if (!currentClient) return;

                const result = await Swal.fire({
                    title: 'Tem certeza?',
                    text: 'Deseja limpar todo o carrinho?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sim, limpar!',
                    cancelButtonText: 'Cancelar'
                });

                if (result.isConfirmed) {
                    try {
                        const response = await fetch('/api/cart/clear', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                store_id: storeId
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            showNotification(data.message, 'success');
                            updateCartCount(0);
                            updateCartTotal(0);
                            loadCart();
                        } else {
                            showNotification(data.message, 'error');
                        }
                    } catch (error) {
                        console.error('Erro ao limpar carrinho:', error);
                        showNotification('Erro ao limpar carrinho', 'error');
                    }
                }
            });

            // Fechar modais
            document.getElementById('close-auth-modal').addEventListener('click', function() {
                document.getElementById('auth-modal').classList.add('hidden');
            });

            document.getElementById('close-cart-modal').addEventListener('click', function() {
                document.getElementById('cart-modal').classList.add('hidden');
            });

            // Controles de quantidade dos produtos
            document.addEventListener('click', function(e) {
                if (e.target.closest('.quantity-btn')) {
                    const button = e.target.closest('.quantity-btn');
                    const productId = button.dataset.product;
                    const input = document.querySelector(`.quantity-input[data-product="${productId}"]`);
                    const isPlus = button.classList.contains('plus');
                    const currentValue = parseInt(input.value);
                    const max = parseInt(input.max);

                    if (isPlus && currentValue < max) {
                        input.value = currentValue + 1;
                    } else if (!isPlus && currentValue > 1) {
                        input.value = currentValue - 1;
                    }
                }
            });

            // Adicionar ao carrinho
            document.addEventListener('click', function(e) {
                if (e.target.closest('.add-to-cart')) {
                    const button = e.target.closest('.add-to-cart');
                    const productId = button.dataset.product;
                    const quantityInput = document.querySelector(`.quantity-input[data-product="${productId}"]`);
                    const quantity = parseInt(quantityInput.value);

                    if (!currentClient) {
                        showAuthModal();
                        return;
                    }

                    addToCart(productId, quantity);
                }
            });

            // Event delegation para botões do carrinho
            document.addEventListener('click', function(e) {
                if (e.target.closest('.cart-quantity-btn')) {
                    const button = e.target.closest('.cart-quantity-btn');
                    const productId = button.dataset.product;
                    const isPlus = button.classList.contains('plus');

                    updateCartQuantity(productId, isPlus);
                }

                if (e.target.closest('.remove-from-cart')) {
                    const button = e.target.closest('.remove-from-cart');
                    const productId = button.dataset.product;

                    removeFromCart(productId);
                }
            });
        }

        // Verificar autenticação do cliente
        async function checkClientAuth() {
            try {
                const response = await fetch('/client/user', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.client) {
                        currentClient = data.client;
                        updateClientUI(true);
                        loadCart();
                    } else {
                        updateClientUI(false);
                    }
                } else {
                    updateClientUI(false);
                }
            } catch (error) {
                console.error('Erro ao verificar autenticação:', error);
                updateClientUI(false);
            }
        }

        // Atualizar interface do cliente
        function updateClientUI(isLoggedIn) {
            const loginBtn = document.getElementById('login-btn');
            const clientStatus = document.getElementById('client-status');
            const clientName = document.getElementById('client-name');
            const logoutBtn = document.getElementById('logout-btn');

            if (isLoggedIn && currentClient) {
                loginBtn.classList.add('hidden');
                clientName.textContent = `Olá, ${currentClient.name}`;
                clientName.classList.remove('hidden');
                logoutBtn.classList.remove('hidden');
            } else {
                loginBtn.classList.remove('hidden');
                clientName.classList.add('hidden');
                logoutBtn.classList.add('hidden');
                currentClient = null;
            }
        }

        // Logout
        async function logout() {
            try {
                const response = await fetch('/client/logout', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    updateClientUI(false);
                    updateCartCount(0);
                    displayCartItems([]);
                    updateCartTotal(0);
                    showNotification('Logout realizado com sucesso!', 'success');
                }
            } catch (error) {
                console.error('Erro ao fazer logout:', error);
            }
        }

        // Adicionar produto ao carrinho
        async function addToCart(productId, quantity) {
            try {
                const response = await fetch('/api/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showNotification(data.message, 'success');
                    updateCartCount(data.cart_quantity);
                    loadCart(); // Recarregar carrinho
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Erro ao adicionar ao carrinho:', error);
                showNotification('Erro ao adicionar produto ao carrinho', 'error');
            }
        }

        // Atualizar quantidade no carrinho
        async function updateCartQuantity(productId, isPlus) {
            try {
                // Primeiro, obter a quantidade atual
                const cartItems = await getCurrentCartItems();
                const currentItem = cartItems.find(item => item.product.id == productId);

                if (!currentItem) return;

                const newQuantity = isPlus ? currentItem.quantity + 1 : currentItem.quantity - 1;

                // Não permitir quantidade menor que 1
                if (newQuantity < 1) {
                    // Se a quantidade for 0, remover o item
                    removeFromCart(productId);
                    return;
                }

                const response = await fetch('/api/cart/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: newQuantity
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showNotification(data.message, 'success');
                    updateCartCount(data.cart_quantity);
                    updateCartTotal(data.cart_total);
                    loadCart(); // Recarregar carrinho
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Erro ao atualizar quantidade:', error);
                showNotification('Erro ao atualizar quantidade', 'error');
            }
        }

        // Remover produto do carrinho
        async function removeFromCart(productId) {
            try {
                const response = await fetch('/api/cart/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        product_id: productId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showNotification(data.message, 'success');
                    updateCartCount(data.cart_quantity);
                    updateCartTotal(data.cart_total);
                    loadCart(); // Recarregar carrinho
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Erro ao remover produto:', error);
                showNotification('Erro ao remover produto', 'error');
            }
        }

        // Obter itens atuais do carrinho
        async function getCurrentCartItems() {
            try {
                const response = await fetch(`/api/cart/get?store_id=${storeId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        return data.cart.items;
                    }
                }
                return [];
            } catch (error) {
                console.error('Erro ao obter itens do carrinho:', error);
                return [];
            }
        }

        // Carregar carrinho
        async function loadCart() {
            if (!currentClient) return;

            try {
                const response = await fetch(`/api/cart/get?store_id=${storeId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        displayCartItems(data.cart.items);
                        updateCartCount(data.cart.total_quantity);
                        updateCartTotal(data.cart.total);
                    }
                }
            } catch (error) {
                console.error('Erro ao carregar carrinho:', error);
            }
        }

        // Exibir itens do carrinho
        function displayCartItems(items) {
            const cartContent = document.getElementById('cart-content');

            if (items.length === 0) {
                cartContent.innerHTML = '<p class="text-gray-500 text-center">Seu carrinho está vazio</p>';
                return;
            }

            cartContent.innerHTML = items.map(item => `
                <div class="flex items-center space-x-4 p-4 border-b">
                    <div class="w-16 h-16 bg-gray-200 rounded">
                        ${item.product.images && item.product.images.length > 0 
                            ? `<img src="${item.product.images[0]}" alt="${item.product.name}" class="w-full h-full object-cover rounded">`
                            : '<div class="w-full h-full flex items-center justify-center"><svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg></div>'
                        }
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold">${item.product.name}</h4>
                        <p class="text-gray-600">R$ ${parseFloat(item.product.price).toFixed(2).replace('.', ',')}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="cart-quantity-btn minus px-2 py-1 bg-gray-200 rounded" data-product="${item.product.id}">-</button>
                        <span class="px-3">${item.quantity}</span>
                        <button class="cart-quantity-btn plus px-2 py-1 bg-gray-200 rounded" data-product="${item.product.id}">+</button>
                    </div>
                    <button class="remove-from-cart text-red-500 hover:text-red-700" data-product="${item.product.id}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            `).join('');
        }

        // Atualizar contador do carrinho
        function updateCartCount(count) {
            const cartCount = document.getElementById('cart-count');
            if (count > 0) {
                cartCount.textContent = count;
                cartCount.classList.remove('hidden');
            } else {
                cartCount.classList.add('hidden');
            }
        }

        // Atualizar total do carrinho
        function updateCartTotal(total) {
            const cartTotal = document.getElementById('cart-total');
            cartTotal.textContent = `R$ ${parseFloat(total).toFixed(2).replace('.', ',')}`;
        }

        // Mostrar modal de autenticação
        function showAuthModal() {
            document.getElementById('auth-modal').classList.remove('hidden');
        }

        // Mostrar notificação usando SweetAlert2
        function showNotification(message, type = 'info') {
            const config = {
                title: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            };

            switch (type) {
                case 'success':
                    config.icon = 'success';
                    break;
                case 'error':
                    config.icon = 'error';
                    break;
                case 'warning':
                    config.icon = 'warning';
                    break;
                default:
                    config.icon = 'info';
            }

            Swal.fire(config);
        }
    </script>
</body>

</html>