<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex items-center justify-center h-screen">
    <div class="text-center">
        <h1 class="text-4xl font-bold mb-4">Bem-vindo à Simple Shop</h1>
        <p class="text-lg mb-6">Sua loja virtual com estilo dark.</p>
        <a href="{{ url('/admin') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
            Admin
        </a>
    </div>
</body>
</html>
