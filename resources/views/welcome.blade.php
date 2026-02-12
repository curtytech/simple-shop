<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop You - Sua loja online</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
  
  <script>
    tailwind = {
      config: {
        darkMode: 'class'
      }
    }
  </script>

 </head>

<body class="bg-gray-50 dark:bg-gray-900 font-sans text-gray-900 dark:text-gray-100">
  <!-- HEADER -->
  <header class="bg-white  shadow-sm border-b border-gray-200   dark:border-slate-950 dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">

        <div class="flex items-center">
          <img src="/favicon.ico" class="h-9 w-9 mr-2" alt="">
          <h1 class="text-2xl font-bold bg-gradient-to-r from-orange-400 via-pink-500 to-purple-600 bg-clip-text text-transparent">
            ShopYou
          </h1>
        </div>

        <nav class="hidden md:flex space-x-8 items-center">
          <a href="#lojas" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Lojas</a>
          <a href="#categorias" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Categorias</a>
          <a href="#sobre" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Sobre</a>

          <button onclick="toggleTheme()" class="p-2 rounded-lg text-lg flex items-center justify-center">
            <i id="theme-icon" class="fa-solid fa-moon" title= "Alterar Tema"></i>
          </button>

        </nav>

      </div>
    </div>
  </header>

  <!-- HERO -->
  <section class="bg-gradient-to-br from-indigo-600 via-purple-600 to-fuchsia-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">

      <h2 class="text-4xl md:text-6xl font-bold mb-6">
        Descubra as Melhores
        <span class="bg-clip-text text-transparent bg-gradient-to-r from-yellow-400 to-orange-500">
          Lojas Online
        </span>
      </h2>

      <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto">
        Conectamos você às melhores lojas locais. Compre com segurança e praticidade em um só lugar.
      </p>

      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="#lojas" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50">
          Explorar Lojas
        </a>

        <a href="#sobre" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600">
          Saiba Mais
        </a>
      </div>

    </div>
  </section>

  <!-- STATS -->
  <section class="py-16 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">

        <div>
          <div class="text-4xl font-bold text-blue-600">{{ $stores->count() }}</div>
          <p class="text-gray-600 dark:text-gray-400">Lojas Parceiras</p>
        </div>

        <div>
          <div class="text-4xl font-bold text-green-600">{{ $totalProducts }}</div>
          <p class="text-gray-600 dark:text-gray-400">Produtos Disponíveis</p>
        </div>

        <div>
          <div class="text-4xl font-bold text-purple-600">{{ $totalCategories }}</div>
          <p class="text-gray-600 dark:text-gray-400">Categorias</p>
        </div>

      </div>

    </div>
  </section>
  <!-- LOJAS -->
  <section id="lojas" class="py-20 bg-gray-50 dark:bg-slate-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- TÍTULO -->
      <div class="text-center mb-16">
        <h3 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
          Nossas Lojas Parceiras
        </h3>

        <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
          Conheça as lojas que fazem parte do nosso marketplace e descubra produtos incríveis
        </p>
      </div>

      @if($stores->count() > 0)
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

        @foreach($stores as $store)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden group">

          <!-- BANNER -->
          <div class="h-32 bg-gradient-to-r from-blue-500 to-purple-600 relative">

            @if($store->banner)
            <img
              src="{{ Storage::url($store->banner) }}"
              alt="Banner {{ $store->name }}"
              class="w-full h-full object-cover">
            @endif

            <div class="absolute inset-0 bg-black bg-opacity-20"></div>
          </div>

          <!-- CONTEÚDO -->
          <div class="relative px-6 pb-6">

            <!-- LOGO -->
            <div class="flex items-start -mt-8 mb-4">
              <div class="w-16 h-16 rounded-full border-4 border-white dark:border-gray-800 shadow-lg overflow-hidden bg-white dark:bg-gray-700">

                @if($store->logo)
                <img
                  src="{{ Storage::url($store->logo) }}"
                  alt="Logo {{ $store->name }}"
                  class="w-full h-full object-cover">
                @else
                <div class="w-full h-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                  <svg class="w-8 h-8 text-gray-400 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3" />
                  </svg>
                </div>
                @endif

              </div>
            </div>

            <!-- INFO -->
            <div class="mb-4">

              <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                {{ $store->name }}
              </h4>

              @if($store->slogan)
              <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">
                {{ $store->slogan }}
              </p>
              @endif

              @if($store->city && $store->state)
              <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                </svg>
                {{ $store->city }}, {{ $store->state }}
              </div>
              @endif

              @if($store->celphone)
              <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 5a2 2 0 012-2h3.28l1.5 4.5-2.25 1.1a11 11 0 005.5 5.5l1.1-2.25 4.5 1.5V19a2 2 0 01-2 2h-1C9.7 21 3 14.3 3 6z" />
                </svg>
                {{ $store->celphone }}
              </div>
              @endif

            </div>

            <!-- STATS -->
            <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
              <span>{{ $store->products->count() }} produtos</span>
              <span>{{ $store->categories->count() }} categorias</span>
            </div>

            <!-- BOTÃO -->
            <a href="{{ route('store.show', $store->slug) }}"
              class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
              Visitar Loja
            </a>

          </div>
        </div>
        @endforeach

      </div>
      @endif

    </div>
  </section>

  <!-- CATEGORIAS -->
  <section id="categorias" class="py-20 bg-base dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4">

      <div class="text-center mb-12">
        <h3 id="categorias" class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
          Categorias Populares
        </h3>
        <p class="text-muted text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
          Explore os produtos por categoria
        </p>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-6">

        @foreach ($popularCategories as $category)
        <div class="bg-card p-6 rounded-xl text-center border border-base hover:shadow-md transition">

          {{-- IMAGEM --}}
          @if($category->image)
          <img
            src="{{ $category->image }}"
            alt="{{ $category->name }}"
            class="w-16 h-16 mx-auto mb-4 rounded-lg object-cover">
          @else
          <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
          </div>
          @endif

          {{-- NOME --}}
          <h4 class="font-semibold text-base">
            {{ $category->name }}
          </h4>

          {{-- QTD PRODUTOS --}}
          <p class="text-sm text-muted mt-2">
            {{ $category->products_count }} produtos
          </p>

        </div>
        @endforeach

      </div>

    </div>
  </section>

  <!-- About Section -->
  <section id="sobre" class="py-20 bg-gray-50 dark:bg-slate-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

        <!-- TEXTO -->
        <div>
          <h3 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-6">
            Por que escolher o Shop You?
          </h3>

          <div class="space-y-6">

            <!-- ITEM 1 -->
            <div class="flex items-start">
              <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
              </div>

              <div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                  Lojas Verificadas
                </h4>
                <p class="text-gray-600 dark:text-gray-400">
                  Todas as lojas passam por um processo de verificação rigoroso para garantir qualidade e confiança.
                </p>
              </div>
            </div>

            <!-- ITEM 2 -->
            <div class="flex items-start">
              <div class="flex-shrink-0 w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
              </div>

              <div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                  Compra Segura
                </h4>
                <p class="text-gray-600 dark:text-gray-400">
                  Sistema de pagamento seguro e proteção ao consumidor em todas as transações.
                </p>
              </div>
            </div>

            <!-- ITEM 3 -->
            <div class="flex items-start">
              <div class="flex-shrink-0 w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
              </div>

              <div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                  Entrega Rápida
                </h4>
                <p class="text-gray-600 dark:text-gray-400">
                  Conectamos você com lojas locais para entregas mais rápidas e eficientes.
                </p>
              </div>
            </div>

          </div>
        </div>

        <!-- CARD DIREITA -->
        <div class="lg:text-center">
          <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl p-8 text-white">

            <h4 class="text-2xl font-bold mb-4">
              Junte-se ao Shop You
            </h4>

            <p class="text-blue-100 mb-6">
              Descubra produtos incríveis de lojas locais e apoie o comércio da sua região.
            </p>

            <a href="#lojas"
              class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors inline-block">
              Começar Agora
            </a>

          </div>
        </div>

      </div>
    </div>
  </section>


  <!-- Footer -->
  <footer class="bg-gray-900 text-white dark:bg-slate-900   py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <h5 class="text-xl font-bold mb-4">Shop You</h5>
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
        <p>&copy; {{ date('Y') }} Shop You. Todos os direitos reservados.</p>
      </div>
    </div>
  </footer>

 <script>
  const themeIcon = document.getElementById('theme-icon')

  function updateThemeIcon() {
    if (!themeIcon) return

    themeIcon.classList.remove('fa-sun', 'fa-moon')

    if (document.documentElement.classList.contains('dark')) {
      themeIcon.classList.add('fa-sun')
    } else {
      themeIcon.classList.add('fa-moon')
    }
  }

  (function() {
    const STORAGE_KEY = 'theme'
    const root = document.documentElement

    const savedTheme = localStorage.getItem(STORAGE_KEY)

    const isDark =
      savedTheme === 'dark' ||
      (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)

    root.classList.toggle('dark', isDark)

    updateThemeIcon()

    window.toggleTheme = function() {
      const isDarkNow = root.classList.toggle('dark')
      localStorage.setItem(STORAGE_KEY, isDarkNow ? 'dark' : 'light')

      updateThemeIcon()
    }
  })()
</script>



</body>

</html>