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

<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">

    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">

            <h2 class="text-2xl font-bold text-center mb-6">
                Login
            </h2>

            <form id="loginForm">
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        E-mail
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600
                               rounded-md shadow-sm bg-white dark:bg-gray-700
                               focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Senha
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600
                               rounded-md shadow-sm bg-white dark:bg-gray-700
                               focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        >
                        <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Lembrar-me
                        </label>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full flex justify-center py-2 px-4 rounded-md shadow-sm
                           text-sm font-medium text-white bg-blue-600 hover:bg-blue-700
                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    Entrar
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Não tem uma conta?
                    <a href="{{ route('client.register') }}"
                       class="font-medium text-blue-600 hover:text-blue-500">
                        Cadastre-se
                    </a>
                </p>
            </div>
        </div>
    </div>

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
