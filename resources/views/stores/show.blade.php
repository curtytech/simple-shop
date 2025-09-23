<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $store->name }} - Loja</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <style>
        .swiper {
            width: 100%;
            padding: 20px 0;
        }
        .swiper-slide {
            width: auto;
        }
        .category-filter.active {
            background-color: #3B82F6;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Banner da Loja -->
    <div class="w-full h-64 relative">
        @if($store->banner)
            <img src="{{ $store->banner }}" alt="Banner da {{ $store->name }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-r from-blue-600 to-purple-700 flex items-center justify-center">
                <svg class="w-24 h-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 7h10M7 11h10M7 15h10"></path>
                </svg>
            </div>
        @endif
        
        <!-- Logo da Loja -->
        <div class="absolute -bottom-16 left-8">
            <div class="flex flex-row items-end">
                @if($store->logo)
                    <img src="{{ $store->logo }}" alt="{{ $store->name }}" class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover">
                @else
                    <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg bg-gray-800 flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 7h10M7 11h10M7 15h10"></path>
                        </svg>
                    </div>
                @endif
                <div class="ml-5 mb-4">
                    <h1 class="text-white text-3xl font-bold drop-shadow-lg">{{ $store->name }}</h1>
                    @if($store->slogan)
                        <p class="text-white text-lg drop-shadow-md">{{ $store->slogan }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Informações da Loja -->
    <div class="container mx-auto px-4 mt-20">
        <!-- Informações de Contato -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Informações da Loja</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($store->email)
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-gray-700">{{ $store->email }}</span>
                    </div>
                @endif
                @if($store->phone)
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span class="text-gray-700">{{ $store->phone }}</span>
                    </div>
                @endif
                @if($store->celphone)
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span class="text-gray-700">{{ $store->celphone }}</span>
                    </div>
                @endif
                @if($store->celphone)
                    <a href="tel:{{ $store->celphone }}" class="text-gray-300 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </a>
                @endif
            </div>
        </div>

        <!-- Filtros de Categoria -->
        @if($store->categories->count() > 0)
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Categorias</h2>
                <div class="flex flex-wrap gap-2 mb-6">
                    <button class="category-filter active px-4 py-2 rounded-full bg-gray-200 text-gray-700 hover:bg-blue-500 hover:text-white transition-colors" data-category="all">
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
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Informações do Produto -->
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $product->name }}</h3>
                                <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $product->description }}</p>
                                
                                <!-- Preço e Estoque -->
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-xl font-bold text-green-600">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                                    <span class="text-sm text-gray-500">
                                        @if($product->stock > 0)
                                            <span class="text-green-600">{{ $product->stock }} em estoque</span>
                                        @else
                                            <span class="text-red-600">Sem estoque</span>
                                        @endif
                                    </span>
                                </div>
                                
                                <!-- Categoria -->
                                <div class="flex justify-between items-center">
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                        {{ $product->category->name }}
                                    </span>
                                    @if($product->bar_code)
                                        <span class="text-xs text-gray-400">{{ $product->bar_code }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Nenhum produto encontrado</h3>
                <p class="text-gray-500">Esta loja ainda não possui produtos cadastrados.</p>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <h3 class="text-xl font-semibold mb-2">{{ $store->name }}</h3>
            @if($store->slogan)
                <p class="text-gray-300 mb-4">{{ $store->slogan }}</p>
            @endif
            <div class="flex justify-center space-x-6">
                @if($store->email)
                    <a href="mailto:{{ $store->email }}" class="text-gray-300 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </a>
                @endif
                @if($store->phone)
                    <a href="tel:{{ $store->phone }}" class="text-gray-300 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </footer>

    <script>
        // Filtro de categorias
        document.addEventListener('DOMContentLoaded', function() {
            const categoryFilters = document.querySelectorAll('.category-filter');
            const productCards = document.querySelectorAll('.product-card');

            categoryFilters.forEach(filter => {
                filter.addEventListener('click', function() {
                    const category = this.getAttribute('data-category');
                    
                    // Remove active class from all filters
                    categoryFilters.forEach(f => f.classList.remove('active'));
                    // Add active class to clicked filter
                    this.classList.add('active');
                    
                    // Show/hide products based on category
                    productCards.forEach(card => {
                        if (category === 'all' || card.getAttribute('data-category') === category) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>