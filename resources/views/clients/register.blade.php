<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cadastro - Cliente</title>

    <!-- 🌓 aplica o tema antes do Tailwind -->
    <script>
        (function () {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <script>
        tailwind.config = { darkMode: 'class' }
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/theme.js') }}"></script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">

<div class="min-h-screen py-6 flex justify-center items-center">
    <div class="w-full max-w-xl bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-10">

        <h2 class="text-2xl font-bold text-center mb-6">
            Criar Conta
        </h2>

        <form id="registerForm" class="space-y-4">
            <input type="hidden" name="user_id" id="user_id" value="{{ request('store_id', 2) }}">

            @foreach ([
                ['name','Nome Completo','text'],
                ['email','E-mail','email'],
                ['celphone','Telefone','text'],
            ] as [$field,$label,$type])
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ $label }}
                    </label>
                    <input
                        type="{{ $type }}"
                        name="{{ $field }}"
                        required
                        class="mt-1 w-full px-3 py-2 rounded-md
                               border border-gray-300 dark:border-gray-600
                               bg-white dark:bg-gray-700
                               focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>
            @endforeach

            <div>
                <label class="block text-sm font-medium">Senha</label>
                <input type="password" name="password" required minlength="8"
                       class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
            </div>

            <div>
                <label class="block text-sm font-medium">Confirmar Senha</label>
                <input type="password" name="password_confirmation" required
                       class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
            </div>

            <input type="text" name="address" placeholder="Endereço" required
                   class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">

            <div class="grid grid-cols-2 gap-4">
                <input type="text" name="number" placeholder="Número" required
                       class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                <input type="text" name="zipcode" placeholder="CEP" required
                       class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
            </div>

            <input type="text" name="city" placeholder="Cidade" required
                   class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">

            <div class="grid grid-cols-2 gap-4">
                <input type="text" name="state" placeholder="Estado" required
                       class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                <input type="text" name="country" value="Brasil" required
                       class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
            </div>

            <input type="text" name="reference_point" placeholder="Ponto de referência (opcional)"
                   class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">

            <button class="w-full py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">
                Criar Conta
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
            Já tem uma conta?
            <a href="{{ route('client.login') }}" class="text-blue-600 hover:text-blue-500">
                Faça login
            </a>
        </p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const storeId = urlParams.get('store_id');
    if (storeId) document.getElementById('user_id').value = storeId;

    document.getElementById('registerForm').addEventListener('submit', async e => {
        e.preventDefault();
        const formData = new FormData(e.target);

        const response = await fetch('{{ route("client.register.post") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await response.json();
        alert(data.message || 'Erro');
        if (data.success) location.href = '/';
    });
});
</script>

</body>
</html>
