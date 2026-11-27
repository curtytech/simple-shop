<!DOCTYPE html>
<html lang="pt-BR" class="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $product->name }} - {{ $store->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>
        tailwind = {
            config: {
                darkMode: 'class'
            }
        }
    </script>
</head>

<body class="min-h-screen bg-gray-50 font-sans text-gray-900 dark:bg-gray-900 dark:text-gray-100">
    @include('stores.client-header')

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <a href="{{ route('store.show', $store->slug) }}" class="mb-6 inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
            <span aria-hidden="true">&larr;</span> Voltar para {{ $store->name }}
        </a>

        <div class="grid gap-8 rounded-xl bg-white p-6 text-gray-900 shadow-md dark:bg-gray-800 dark:text-gray-100 md:grid-cols-2 md:p-8">
            <section aria-label="Fotos do produto">
                @if($product->images && count($product->images) > 0)
                <div class="grid grid-cols-2 gap-4">
                    @foreach($product->images as $image)
                    @php($imageUrl = str_starts_with($image, 'http://') || str_starts_with($image, 'https://') ? $image : Storage::url($image))
                    <button
                        type="button"
                        class="product-gallery-image group relative overflow-hidden rounded-lg bg-gray-100 text-left"
                        data-image="{{ $imageUrl }}"
                        aria-label="Ampliar foto do produto {{ $product->name }}">
                        <img
                            src="{{ $imageUrl }}"
                            alt="{{ $product->name }}"
                            class="aspect-square w-full object-cover transition-transform group-hover:scale-105"
                            loading="lazy">
                        <span class="absolute bottom-2 right-2 rounded-full bg-black/60 px-3 py-1 text-sm text-white">Ampliar</span>
                    </button>
                    @endforeach
                </div>
                @else
                <div class="flex aspect-square items-center justify-center rounded-lg bg-gray-100 text-gray-400">
                    <span>Este produto não possui fotos.</span>
                </div>
                @endif
            </section>

            <section>
                @if($product->category)
                <p class="mb-2 text-sm font-medium text-blue-600 dark:text-blue-400">{{ $product->category->name }}</p>
                @endif
                <h1 class="text-3xl font-bold">{{ $product->name }}</h1>
                <p class="mt-4 text-3xl font-bold text-green-700 dark:text-green-400">R$ {{ number_format($product->price, 2, ',', '.') }}</p>

                <p class="mt-3 {{ $product->stock > 0 ? 'text-gray-600 dark:text-gray-300' : 'font-medium text-red-600 dark:text-red-400' }}">
                    {{ $product->stock > 0 ? $product->stock . ' em estoque' : 'Sem estoque' }}
                </p>

                @if($product->stock > 0)
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <div class="flex items-center rounded-lg border">
                        <button type="button" class="quantity-control px-4 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" data-direction="minus" aria-label="Diminuir quantidade">−</button>
                        <input id="product-quantity" type="number" value="1" min="1" max="{{ $product->stock }}" class="w-16 border-0 bg-white text-center text-gray-900 focus:ring-0 dark:bg-gray-700 dark:text-gray-100" aria-label="Quantidade">
                        <button type="button" class="quantity-control px-4 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" data-direction="plus" aria-label="Aumentar quantidade">+</button>
                    </div>
                    <button
                        id="add-product-to-cart"
                        type="button"
                        data-product="{{ $product->id }}"
                        class="flex-1 rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white transition-colors hover:bg-blue-700">
                        Adicionar ao carrinho
                    </button>
                </div>
                @else
                <button type="button" disabled class="mt-6 w-full cursor-not-allowed rounded-lg bg-gray-300 px-6 py-3 font-semibold text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                    Indisponível
                </button>
                @endif

                @if($product->description)
                <div class="mt-6">
                    <h2 class="text-lg font-semibold">Descrição</h2>
                    <p class="mt-2 whitespace-pre-line text-gray-700 dark:text-gray-300">{{ $product->description }}</p>
                </div>
                @endif

                <dl class="mt-6 grid grid-cols-1 gap-3 border-t border-gray-200 pt-5 dark:border-gray-600 sm:grid-cols-2">
                    @if($product->bar_code)
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Código de barras</dt>
                        <dd class="font-medium">{{ $product->bar_code }}</dd>
                    </div>
                    @endif
                    @if($product->type_of_sell)
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Modalidade de venda</dt>
                        <dd class="font-medium">{{ collect($product->type_of_sell)->map(fn ($type) => ['venda_por_entrega' => 'Venda por entrega', 'venda_no_local' => 'Venda no local', 'encomenda' => 'Encomenda'][$type] ?? $type)->join(', ') }}</dd>
                    </div>
                    @endif
                    @if($product->width_cm || $product->height_cm || $product->length_cm)
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Dimensões</dt>
                        <dd class="font-medium">
                            {{ $product->width_cm ? $product->width_cm . ' cm' : '—' }} ×
                            {{ $product->height_cm ? $product->height_cm . ' cm' : '—' }} ×
                            {{ $product->length_cm ? $product->length_cm . ' cm' : '—' }}
                        </dd>
                    </div>
                    @endif
                    @if($product->weight_kg)
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Peso</dt>
                        <dd class="font-medium">{{ $product->weight_kg }} kg</dd>
                    </div>
                    @endif
                </dl>
            </section>
        </div>
    </main>

    <dialog id="store-cart-modal" class="fixed inset-0 m-auto max-h-[85vh] w-full max-w-2xl rounded-lg p-0 backdrop:bg-black/50">
        <div class="flex max-h-[85vh] flex-col bg-white text-gray-900 dark:bg-slate-800 dark:text-gray-100">
            <div class="flex items-center justify-between border-b p-5 dark:border-gray-700">
                <h2 class="text-xl font-semibold">Meu Carrinho</h2>
                <button type="button" id="close-store-cart" class="rounded px-3 py-1 text-2xl leading-none hover:bg-gray-100 dark:hover:bg-slate-700" aria-label="Fechar carrinho">&times;</button>
            </div>
            <div id="store-cart-items" class="min-h-24 flex-1 space-y-3 overflow-y-auto p-5">
                <p class="text-gray-500 dark:text-gray-400">Carregando carrinho...</p>
            </div>
            <div class="flex items-center justify-between border-t p-5 dark:border-gray-700">
                <span class="font-semibold">Total</span>
                <span id="store-cart-total" class="text-xl font-bold text-green-700 dark:text-green-400">R$ 0,00</span>
            </div>
        </div>
    </dialog>

    <dialog id="product-image-modal" class="fixed inset-0 m-auto max-h-none max-w-none bg-transparent p-0 backdrop:bg-black/85">
        <div class="mx-auto flex max-h-[95vh] max-w-[95vw] flex-col items-center rounded-lg bg-white p-4 text-gray-900 shadow-xl dark:bg-slate-800 dark:text-gray-100">
            <div class="mb-3 flex w-full items-center justify-between gap-4">
                <p class="font-medium">{{ $product->name }}</p>
                <div class="flex items-center gap-2">
                    <button type="button" id="zoom-out" class="rounded border px-3 py-1 hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-slate-700" aria-label="Diminuir zoom">−</button>
                    <span id="zoom-level" class="min-w-12 text-center text-sm">100%</span>
                    <button type="button" id="zoom-in" class="rounded border px-3 py-1 hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-slate-700" aria-label="Aumentar zoom">+</button>
                    <button type="button" id="zoom-reset" class="rounded border px-3 py-1 text-sm hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-slate-700">Ajustar</button>
                    <button type="button" id="close-image-modal" class="rounded px-3 py-1 text-2xl leading-none hover:bg-gray-100 dark:hover:bg-slate-700" aria-label="Fechar">&times;</button>
                </div>
            </div>
            <div id="product-image-viewport" class="max-h-[75vh] max-w-[90vw] overflow-auto">
                <img id="product-image-modal-image" alt="{{ $product->name }}" class="mx-auto block">
            </div>
        </div>
    </dialog>

    <script>
        (() => {
            const root = document.documentElement;
            const savedTheme = localStorage.getItem('theme');
            const isDark = savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches);
            root.classList.toggle('dark', isDark);

            function updateThemeIcon(dark) {
                const icon = document.getElementById('theme-icon');
                if (!icon) return;
                icon.classList.remove('fa-sun', 'fa-moon');
                icon.classList.add(dark ? 'fa-sun' : 'fa-moon');
            }

            updateThemeIcon(isDark);
            window.toggleTheme = () => {
                const dark = root.classList.toggle('dark');
                localStorage.setItem('theme', dark ? 'dark' : 'light');
                updateThemeIcon(dark);
            };
        })();

        (() => {
            const modal = document.getElementById('product-image-modal');
            const image = document.getElementById('product-image-modal-image');
            const viewport = document.getElementById('product-image-viewport');
            const zoomLevel = document.getElementById('zoom-level');
            const zoomIn = document.getElementById('zoom-in');
            const zoomOut = document.getElementById('zoom-out');
            let fitWidth = 0;
            let fitHeight = 0;
            let zoom = 1;
            let currentClient = null;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const cartModal = document.getElementById('store-cart-modal');
            const cartItems = document.getElementById('store-cart-items');
            const cartTotal = document.getElementById('store-cart-total');
            const productId = document.getElementById('add-product-to-cart')?.dataset.product;

            function updateImageSize() {
                if (!image.naturalWidth || !image.naturalHeight) return;

                const fit = Math.min(
                    (window.innerWidth * 0.9) / image.naturalWidth,
                    (window.innerHeight * 0.75) / image.naturalHeight,
                    1
                );
                fitWidth = image.naturalWidth * fit;
                fitHeight = image.naturalHeight * fit;
                image.style.width = `${fitWidth * zoom}px`;
                image.style.height = `${fitHeight * zoom}px`;
                zoomLevel.textContent = `${Math.round(zoom * 100)}%`;
                zoomIn.disabled = zoom >= 4;
                zoomOut.disabled = zoom <= 1;
            }

            function notify(message, type = 'info') {
                Swal.fire({
                    title: message,
                    icon: type,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }

            function updateCartCount(count) {
                const badge = document.getElementById('cart-count');
                if (!badge) return;
                badge.textContent = count;
                badge.classList.toggle('hidden', count < 1);
            }

            async function loadCart() {
                const response = await fetch(`/api/cart/get?store_id={{ $store->id }}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Não foi possível carregar o carrinho.');
                }

                cartItems.replaceChildren();
                if (data.cart.items.length === 0) {
                    const emptyMessage = document.createElement('p');
                    emptyMessage.className = 'text-gray-500 dark:text-gray-400';
                    emptyMessage.textContent = 'Seu carrinho está vazio.';
                    cartItems.appendChild(emptyMessage);
                } else {
                    data.cart.items.forEach(item => {
                        const row = document.createElement('div');
                        row.className = 'flex justify-between gap-4 border-b pb-3 dark:border-gray-700';
                        const name = document.createElement('span');
                        name.textContent = `${item.product?.name || 'Produto'} × ${item.quantity}`;
                        const price = document.createElement('span');
                        price.className = 'font-medium';
                        price.textContent = `R$ ${(Number(item.price) * Number(item.quantity)).toFixed(2).replace('.', ',')}`;
                        row.append(name, price);
                        cartItems.appendChild(row);
                    });
                }

                cartTotal.textContent = `R$ ${Number(data.cart.total).toFixed(2).replace('.', ',')}`;
                updateCartCount(data.cart.total_quantity);
            }

            async function addProductToCart() {
                if (!currentClient) {
                    window.location.href = "{{ route('client.login') }}?return={{ urlencode(request()->fullUrl()) }}";
                    return;
                }

                const quantityInput = document.getElementById('product-quantity');
                const button = document.getElementById('add-product-to-cart');
                button.disabled = true;

                try {
                    const response = await fetch('/api/cart/add', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: Number(quantityInput.value)
                        })
                    });
                    const data = await response.json();

                    if (response.status === 401) {
                        window.location.href = "{{ route('client.login') }}?return={{ urlencode(request()->fullUrl()) }}";
                        return;
                    }
                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Não foi possível adicionar o produto ao carrinho.');
                    }

                    updateCartCount(data.cart_quantity);
                    notify(data.message, 'success');
                    if (cartModal.open) await loadCart();
                } catch (error) {
                    notify(error.message || 'Erro ao adicionar produto ao carrinho.', 'error');
                } finally {
                    button.disabled = false;
                }
            }

            function changeZoom(amount) {
                zoom = Math.min(4, Math.max(1, zoom + amount));
                updateImageSize();
            }

            document.querySelectorAll('.quantity-control').forEach(button => {
                button.addEventListener('click', () => {
                    const input = document.getElementById('product-quantity');
                    const next = Number(input.value) + (button.dataset.direction === 'plus' ? 1 : -1);
                    input.value = Math.min(Number(input.max), Math.max(Number(input.min), next));
                });
            });
            document.getElementById('product-quantity')?.addEventListener('change', event => {
                const input = event.currentTarget;
                input.value = Math.min(Number(input.max), Math.max(Number(input.min), Number(input.value) || 1));
            });
            document.getElementById('add-product-to-cart')?.addEventListener('click', addProductToCart);

            fetch('/client/user', { headers: { 'Accept': 'application/json' } })
                .then(response => response.json())
                .then(data => {
                    currentClient = data.client;
                    const loginButton = document.getElementById('login-btn');
                    const logoutButton = document.getElementById('logout-btn');
                    const clientName = document.getElementById('client-name');

                    if (currentClient) {
                        loginButton?.classList.add('hidden');
                        logoutButton?.classList.remove('hidden');
                        if (clientName) {
                            clientName.textContent = `Olá, ${currentClient.name}`;
                            clientName.classList.remove('hidden');
                        }
                        loadCart().catch(error => console.error(error));
                    }
                })
                .catch(error => console.error('Erro ao verificar autenticação:', error));

            document.getElementById('login-btn')?.addEventListener('click', () => {
                window.location.href = "{{ route('client.login') }}?return={{ urlencode(request()->fullUrl()) }}";
            });
            document.getElementById('logout-btn')?.addEventListener('click', async () => {
                try {
                    const response = await fetch('/client/logout', {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                    });
                    if (!response.ok) throw new Error('Não foi possível sair da conta.');
                    window.location.reload();
                } catch (error) {
                    notify(error.message, 'error');
                }
            });
            document.getElementById('cart-btn')?.addEventListener('click', async () => {
                if (!currentClient) {
                    window.location.href = "{{ route('client.login') }}?return={{ urlencode(request()->fullUrl()) }}";
                    return;
                }
                cartModal.showModal();
                try {
                    await loadCart();
                } catch (error) {
                    notify(error.message, 'error');
                }
            });
            document.getElementById('close-store-cart').addEventListener('click', () => cartModal.close());
            cartModal.addEventListener('click', event => {
                if (event.target === cartModal) cartModal.close();
            });

            document.querySelectorAll('.product-gallery-image').forEach(button => {
                button.addEventListener('click', () => {
                    zoom = 1;
                    image.src = button.dataset.image;
                    modal.showModal();
                    if (image.complete) updateImageSize();
                });
            });

            image.addEventListener('load', updateImageSize);
            zoomIn.addEventListener('click', () => changeZoom(0.5));
            zoomOut.addEventListener('click', () => changeZoom(-0.5));
            document.getElementById('zoom-reset').addEventListener('click', () => {
                zoom = 1;
                updateImageSize();
                viewport.scrollTo({ top: 0, left: 0 });
            });
            document.getElementById('close-image-modal').addEventListener('click', () => modal.close());
            modal.addEventListener('click', event => {
                if (event.target === modal) modal.close();
            });
            window.addEventListener('resize', updateImageSize);
        })();
    </script>
</body>

</html>
