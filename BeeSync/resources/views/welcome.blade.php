<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BeeSync - Gestão Inteligente de Apiários</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-100">
    <div class="relative min-h-screen flex flex-col items-center justify-center">

        <div class="absolute top-0 right-0 p-6">
            @auth
                <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-gray-900">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900">Entrar</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="ml-4 font-semibold text-gray-600 hover:text-gray-900">Cadastrar</a>
                @endif
            @endauth
        </div>

        <div class="max-w-xl mx-auto p-6 text-center">

            <a href="/">
                <img src="{{ asset('images/BeeSyncLogo.png') }}" alt="Logo do Sistema"
                    class="w-24 h-24 mx-auto mb-4 fill-current text-yellow-500">
            </a>

            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                Bem-vindo ao BeeSync
            </h1>

            <p class="text-lg text-gray-700 mb-6">
                Monitore a saúde das suas colmeias e otimize sua produção de mel.
                Tome decisões baseadas em dados.
            </p>

            <a href="{{ route('register') }}"
                class="inline-block bg-yellow-500 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:bg-yellow-600 transition-colors">
                Comece a monitorar agora
            </a>

            <p class="mt-4 text-sm text-gray-500">
                Já tem uma conta? <a href="{{ route('login') }}" class="text-yellow-600 hover:underline">Faça login</a>.
            </p>
        </div>

        <div class="absolute bottom-0 left-0 p-6 text-sm text-gray-500">
            Projeto para o CODE DAY 2025 [cite: 2] / SBI [cite: 3]
        </div>
    </div>
</body>

</html>
