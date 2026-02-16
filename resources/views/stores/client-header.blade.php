    <header class="bg-white shadow-sm border-b border-gray-200 dark:border-slate-950 dark:bg-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- LOGO -->
                <div class="flex items-center">
                    <a href="{{config('app.url')}}" class="flex items-center">
                        <img src="/favicon.ico" class="h-9 w-9 mr-2" alt="">
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-orange-400 via-pink-500 to-purple-600 bg-clip-text text-transparent">
                            ShopYou
                        </h1>
                    </a>
                </div>

                <!-- NAV PRINCIPAL (HOME / PRODUTOS) -->
                <nav class="hidden md:flex items-center gap-8">

                    <a href="#categories-title" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                        Categorias
                    </a>

                    <a href="#products-title" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                        Produtos
                    </a>

                    <a href="#sobre" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                        Sobre
                    </a>
                </nav>

                <!-- NAV DE AÇÕES -->
                <div class="hidden md:flex items-center gap-4">

                    <!-- Perfil -->
                    @if(auth()->guard('client')->check())
                    <span id="client-name" class="hidden"></span>    
                    <a href="{{ route('store.client.config', $store->slug) }}" class=" mb-1  p-2 text-lg text-gray-800 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                          <i class="fa-regular fa-user"></i>
                    </a>
                    @endif

                    <!-- Carrinho (usa a lógica existente) -->
                    <button
                        id="cart-btn"
                        class="relative p-2 text-gray-800 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition"
                        title="Carrinho">
                        <!-- Ícone -->
                        <i class="fa-solid fa-cart-shopping"></i>
                        <!-- Badge (continua funcionando) -->
                        <span
                            id="cart-count"
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full hidden">
                            0
                        </span>
                    </button>

                    <a
                        id="cart-btn"
                        class="relative p-2 text-gray-800 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition"
                        title="Minhas Compras"
                        href="{{ route('store.client.products', $store->slug) }}">                        
                        <i class="fa-solid fa-bag-shopping"></i>                      
                    </a>

                    <!-- Divider -->
                    <span class="h-6 w-px bg-gray-200 dark:bg-gray-700"></span>

                    <!-- Toggle Theme -->
                    <button onclick="toggleTheme()" class="p-2">
                        <i id="theme-icon" class="fa-solid fa-moon " title= "Alterar Tema"></i>
                    </button>

                   <button
                        id="login-btn"
                        class="text-gray-800 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                        Entrar
                    </button>

                <button
                    id="logout-btn"
                    class="hidden text-red-500 hover:text-red-700">
                    Sair
                </button>
                </div>
            </div>
        </div>
    </header>
