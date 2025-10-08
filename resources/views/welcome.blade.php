<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Shop - Marketplace de Lojas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'ui-sans-serif', 'system-ui'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Simple Shop</h1>
                    <span class="ml-2 text-sm text-gray-500">Marketplace</span>
                </div>
                <nav class="hidden md:flex space-x-8">
                    <a href="#lojas" class="text-gray-600 hover:text-gray-900 transition-colors">Lojas</a>
                    <a href="#categorias" class="text-gray-600 hover:text-gray-900 transition-colors">Categorias</a>
                    <a href="#sobre" class="text-gray-600 hover:text-gray-900 transition-colors">Sobre</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-blue-600 via-purple-600 to-indigo-700 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h2 class="text-4xl md:text-6xl font-bold mb-6">
                    Descubra as Melhores
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500">
                        Lojas Online
                    </span>
                </h2>
                <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto">
                    Conectamos você às melhores lojas locais. Compre com segurança e praticidade em um só lugar.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#lojas" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                        Explorar Lojas
                    </a>
                    <a href="#sobre" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                        Saiba Mais
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="p-6">
                    <div class="text-4xl font-bold text-blue-600 mb-2" id="stores-count">{{ $stores->count() }}</div>
                    <div class="text-gray-600">Lojas Parceiras</div>
                </div>
                <div class="p-6">
                    <div class="text-4xl font-bold text-green-600 mb-2" id="products-count">{{ $totalProducts }}</div>
                    <div class="text-gray-600">Produtos Disponíveis</div>
                </div>
                <div class="p-6">
                    <div class="text-4xl font-bold text-purple-600 mb-2" id="categories-count">{{ $totalCategories }}</div>
                    <div class="text-gray-600">Categorias</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Stores Section -->
    <section id="lojas" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h3 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Nossas Lojas Parceiras
                </h3>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Conheça as lojas que fazem parte do nosso marketplace e descubra produtos incríveis
                </p>
            </div>

            @if($stores->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($stores as $store)
                        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden group">
                            <!-- Store Banner/Header -->
                            <div class="h-32 bg-gradient-to-r from-blue-500 to-purple-600 relative">
                                @if($store->banner)
                                    <img src="{{ $store->banner }}" alt="Banner {{ $store->name }}" class="w-full h-full object-cover">
                                @endif
                                <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                            </div>

                            <!-- Store Logo -->
                            <div class="relative px-6 pb-6">
                                <div class="flex items-start -mt-8 mb-4">
                                    <div class="w-16 h-16 rounded-full border-4 border-white shadow-lg overflow-hidden bg-white">
                                        @if($store->logo)
                                            <img src="{{ $store->logo }}" alt="Logo {{ $store->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h4M9 7h6m-6 4h6m-6 4h6"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Store Info -->
                                <div class="mb-4">
                                    <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $store->name }}</h4>
                                    @if($store->slogan)
                                        <p class="text-gray-600 text-sm mb-3">{{ $store->slogan }}</p>
                                    @endif
                                    
                                    <div class="flex items-center text-sm text-gray-500 mb-2">
                                        @if($store->city && $store->state)
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            {{ $store->city }}, {{ $store->state }}
                                        @endif
                                    </div>

                                    @if($store->celphone)
                                        <div class="flex items-center text-sm text-gray-500 mb-3">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                            {{ $store->celphone }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Store Stats -->
                                <div class="flex justify-between text-sm text-gray-500 mb-4">
                                    <span>{{ $store->products->count() }} produtos</span>
                                    <span>{{ $store->categories->count() }} categorias</span>
                                </div>

                                <!-- Action Button -->
                                <a href="{{ route('store.show', $store->slug) }}" 
                                   class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors group-hover:bg-blue-700">
                                    Visitar Loja
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h4M9 7h6m-6 4h6m-6 4h6"></path>
                    </svg>
                    <h4 class="text-xl font-semibold text-gray-900 mb-2">Nenhuma loja encontrada</h4>
                    <p class="text-gray-600">Em breve teremos lojas incríveis para você!</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categorias" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h3 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Categorias Populares
                </h3>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Explore produtos por categoria e encontre exatamente o que você procura
                </p>
            </div>

            @if($popularCategories->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($popularCategories as $category)
                        <div class="bg-gray-50 rounded-xl p-6 text-center hover:bg-gray-100 transition-colors cursor-pointer">
                            @if($category->image)
                                <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-16 h-16 mx-auto mb-4 rounded-lg object-cover">
                            @else
                                <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                            @endif
                            <h4 class="font-semibold text-gray-900 mb-2">{{ $category->name }}</h4>
                            <p class="text-sm text-gray-600">{{ $category->products_count }} produtos</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- About Section -->
    <section id="sobre" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h3 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                        Por que escolher o Simple Shop?
                    </h3>
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Lojas Verificadas</h4>
                                <p class="text-gray-600">Todas as lojas passam por um processo de verificação rigoroso para garantir qualidade e confiança.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Compra Segura</h4>
                                <p class="text-gray-600">Sistema de pagamento seguro e proteção ao consumidor em todas as transações.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Entrega Rápida</h4>
                                <p class="text-gray-600">Conectamos você com lojas locais para entregas mais rápidas e eficientes.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="lg:text-center">
                    <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl p-8 text-white">
                        <h4 class="text-2xl font-bold mb-4">Junte-se ao Simple Shop</h4>
                        <p class="text-blue-100 mb-6">Descubra produtos incríveis de lojas locais e apoie o comércio da sua região.</p>
                        <a href="#lojas" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors inline-block">
                            Começar Agora
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h5 class="text-xl font-bold mb-4">Simple Shop</h5>
                    <p class="text-gray-400 mb-4">Conectando você às melhores lojas locais com segurança e praticidade.</p>
                </div>
                <div>
                    <h6 class="font-semibold mb-4">Links Úteis</h6>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#lojas" class="hover:text-white transition-colors">Lojas</a></li>
                        <li><a href="#categorias" class="hover:text-white transition-colors">Categorias</a></li>
                        <li><a href="#sobre" class="hover:text-white transition-colors">Sobre</a></li>
                    </ul>
                </div>
                <div>
                    <h6 class="font-semibold mb-4">Contato</h6>
                    <p class="text-gray-400">Entre em contato conosco para mais informações sobre o marketplace.</p>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} Simple Shop. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>