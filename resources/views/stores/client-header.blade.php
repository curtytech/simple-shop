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

                    <!-- Notificações do cliente -->
                    <div class="relative" id="customer-notifications-wrapper">
                        <button
                            id="customer-notifications-btn"
                            type="button"
                            class="relative p-2 text-gray-800 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition"
                            title="Notificações"
                            aria-label="Abrir notificações">
                            <i class="fa-solid fa-bell"></i>
                            <span id="customer-notifications-count" class="absolute -top-1 -right-1 hidden min-w-4 rounded-full bg-red-500 px-1 text-center text-xs text-white"></span>
                        </button>
                        <div id="customer-notifications-dropdown" class="absolute right-0 z-50 mt-2 hidden w-80 max-w-[90vw] rounded-lg border border-gray-200 bg-white text-gray-800 shadow-xl dark:border-gray-700 dark:bg-slate-800 dark:text-gray-100">
                            <div class="flex items-center justify-between border-b p-3">
                                <strong>Notificações</strong>
                                <button id="customer-notifications-read-all" type="button" class="text-xs text-blue-600 hover:underline dark:text-blue-400">Marcar todas como lidas</button>
                            </div>
                            <div id="customer-notifications-list" class="max-h-96 overflow-y-auto p-2">
                                <p class="p-3 text-sm text-gray-500">Carregando...</p>
                            </div>
                        </div>
                    </div>

                    <a
                        id="purchases-btn"
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

    <script>
        (() => {
            const button = document.getElementById('customer-notifications-btn');
            const dropdown = document.getElementById('customer-notifications-dropdown');
            const list = document.getElementById('customer-notifications-list');
            const count = document.getElementById('customer-notifications-count');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            if (!button || !dropdown || !list) return;

            const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
            }[char]));

            async function loadNotifications() {
                try {
                    const response = await fetch('/api/cart/customer-notifications', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await response.json();

                    if (!response.ok) {
                        list.innerHTML = `<p class="p-3 text-sm text-gray-500">${escapeHtml(data.message || 'Entre na sua conta para ver as notificações.')}</p>`;
                        count.classList.add('hidden');
                        return;
                    }

                    count.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                    count.classList.toggle('hidden', data.unread_count === 0);
                    list.innerHTML = data.notifications.length ? data.notifications.map(notification => {
                        const safeUrl = notification.action_url && new URL(notification.action_url, window.location.origin).origin === window.location.origin
                            ? notification.action_url
                            : '';
                        const date = notification.created_at ? new Date(notification.created_at).toLocaleString('pt-BR') : '';

                        return `<div class="mb-2 flex gap-2 rounded-lg p-3 ${notification.read_at ? 'bg-gray-50 dark:bg-slate-900' : 'bg-blue-50 dark:bg-slate-700'}">
                            <button type="button" class="notification-open min-w-0 flex-1 text-left" data-id="${notification.id}" data-url="${escapeHtml(safeUrl)}">
                                <strong class="block text-sm">${escapeHtml(notification.title)}</strong>
                                <span class="mt-1 block text-sm">${escapeHtml(notification.message)}</span>
                                <small class="mt-1 block text-gray-500">${escapeHtml(date)}</small>
                            </button>
                            <button type="button" class="notification-delete self-start px-1 text-gray-500 hover:text-red-600" data-id="${notification.id}" aria-label="Excluir notificação">&times;</button>
                        </div>`;
                    }).join('') : '<p class="p-3 text-sm text-gray-500">Você não tem notificações.</p>';
                } catch (error) {
                    list.innerHTML = '<p class="p-3 text-sm text-red-600">Não foi possível carregar as notificações.</p>';
                }
            }

            button.addEventListener('click', () => {
                dropdown.classList.toggle('hidden');
                if (!dropdown.classList.contains('hidden')) loadNotifications();
            });

            document.getElementById('customer-notifications-read-all')?.addEventListener('click', async () => {
                await fetch('/api/cart/customer-notifications/read-all', {
                    method: 'PATCH',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                loadNotifications();
            });

            list.addEventListener('click', async event => {
                const openButton = event.target.closest('.notification-open');
                const deleteButton = event.target.closest('.notification-delete');

                if (openButton) {
                    await fetch(`/api/cart/customer-notifications/${openButton.dataset.id}/read`, {
                        method: 'PATCH',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                    });
                    if (openButton.dataset.url) window.location.href = openButton.dataset.url;
                    else loadNotifications();
                }

                if (deleteButton) {
                    await fetch(`/api/cart/customer-notifications/${deleteButton.dataset.id}`, {
                        method: 'DELETE',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                    });
                    loadNotifications();
                }
            });

            window.loadCustomerNotifications = loadNotifications;
            loadNotifications();
        })();
    </script>
