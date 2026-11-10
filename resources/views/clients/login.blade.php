<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Cliente</title>

    <!-- 🌓 Aplica o tema ANTES do Tailwind -->
    <script>
        (function () {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <!-- Tailwind config -->
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Theme global -->
    <script src="{{ asset('js/theme.js') }}"></script>
</head>

<body class="bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">

    <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10 sm:px-6">
        <div class="pointer-events-none absolute -left-32 -top-32 h-72 w-72 rounded-full bg-orange-200/50 blur-3xl dark:bg-orange-950/30"></div>
        <div class="pointer-events-none absolute -bottom-40 -right-32 h-80 w-80 rounded-full bg-purple-200/50 blur-3xl dark:bg-purple-950/30"></div>

        <section class="relative w-full max-w-md">
            <div class="mb-8 text-center">
                <a href="{{ config('app.url') }}" class="inline-flex items-center gap-2" aria-label="Ir para a página inicial">
                    <img src="/favicon.ico" class="h-9 w-9" alt="">
                    <span class="bg-gradient-to-r from-orange-400 via-pink-500 to-purple-600 bg-clip-text text-2xl font-bold text-transparent">
                        ShopYou
                    </span>
                </a>
                <p class="mt-5 text-sm text-slate-500 dark:text-slate-400">
                    Entre para continuar suas compras
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/60 sm:p-8 dark:border-slate-800 dark:bg-slate-900 dark:shadow-black/20">
                <div class="mb-7">
                    <h1 class="text-2xl font-semibold tracking-tight">Acesse sua conta</h1>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                        Informe seus dados para entrar.
                    </p>
                </div>

                <form id="loginForm" class="space-y-5">
                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
                            E-mail
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            autocomplete="email"
                            required
                            class="block w-full rounded-lg border border-slate-300 bg-slate-50 px-3.5 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-pink-500 focus:ring-4 focus:ring-pink-500/10 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-pink-400"
                            placeholder="voce@exemplo.com"
                        >
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
                            Senha
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            autocomplete="current-password"
                            required
                            class="block w-full rounded-lg border border-slate-300 bg-slate-50 px-3.5 py-3 text-sm text-slate-900 outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-500/10 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-pink-400"
                            placeholder="Digite sua senha"
                        >
                    </div>

                    <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            class="h-4 w-4 rounded border-slate-300 text-pink-600 focus:ring-pink-500 dark:border-slate-600 dark:bg-slate-800"
                        >
                        Lembrar-me
                    </label>

                    <button
                        type="submit"
                        class="w-full rounded-lg bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-pink-500/20 transition hover:brightness-105 focus:outline-none focus:ring-4 focus:ring-pink-500/25 active:scale-[.99]"
                    >
                        Entrar
                    </button>
                </form>

                <div class="mt-7 border-t border-slate-100 pt-6 text-center dark:border-slate-800">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Não tem uma conta?
                        <a href="{{ route('client.register') }}" class="font-semibold text-pink-600 transition hover:text-purple-600 dark:text-pink-400 dark:hover:text-purple-400">
                            Cadastre-se
                        </a>
                    </p>
                </div>
            </div>
        </section>
    </main>

    <!-- Login JS -->
    <script>
        document.getElementById('loginForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            try {
                const response = await fetch('{{ route("client.login.post") }}', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: data.message
                    });

                    const returnUrl =
                        new URLSearchParams(window.location.search).get('return') || '/';

                    window.location.href = returnUrl;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: data.message
                    });
                }
            } catch {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro ao fazer login. Tente novamente.'
                });
            }
        });
    </script>

</body>
</html>
