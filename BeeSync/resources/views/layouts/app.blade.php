<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA Meta Tags -->
    <meta name="application-name" content="BeeSync">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="BeeSync">
    <meta name="description" content="Gerencie seus apiários e realize inspeções mesmo sem internet">
    <meta name="theme-color" content="#FBBF24">
    <meta name="mobile-web-app-capable" content="yes">

    <!-- PWA Icons -->
    <link rel="apple-touch-icon" href="{{ asset('images/SmallBee.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/SmallBee.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/NormalBee.png') }}">

    <title>{{ config('app.name', 'BeeSync') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <x-toast-feedback />
    <div class="min-h-screen"
        style="background-image: url('{{ asset('images/imagebg.jpg') }}'); background-size: cover; background-position: center; background-attachment: fixed;">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white dark:bg-black shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
    <script>
        // Variável para armazenar o evento beforeinstallprompt
        let deferredPrompt;

        // Listener para o evento beforeinstallprompt
        window.addEventListener('beforeinstallprompt', (e) => {
            // Previne que o prompt apareça automaticamente
            e.preventDefault();
            // Armazena o evento para uso posterior
            deferredPrompt = e;
            // Mostra um botão ou UI para instalar o PWA
            showInstallPromotion();
        });

        // Registra o Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', async () => {
                try {
                    const registration = await navigator.serviceWorker.register('/sw.js');
                    console.log('Service Worker registrado com sucesso:', registration);
                } catch (error) {
                    console.error('Falha ao registrar Service Worker:', error);
                }
            });
        }

        // Função para mostrar o prompt de instalação
        function showInstallPromotion() {
            // Cria o toast de instalação se ele ainda não existir
            if (!document.getElementById('pwa-install-toast')) {
                const toast = document.createElement('div');
                toast.id = 'pwa-install-toast';
                toast.style.cssText = `
                    position: fixed;
                    bottom: 20px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: #FBBF24;
                    color: black;
                    padding: 1rem;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                    z-index: 1000;
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                `;

                toast.innerHTML = `
                    <div>Instale o BeeSync para usar offline!</div>
                    <button onclick="installPWA()" style="
                        background: black;
                        color: white;
                        border: none;
                        padding: 0.5rem 1rem;
                        border-radius: 4px;
                        cursor: pointer;
                    ">Instalar</button>
                    <button onclick="this.parentElement.remove()" style="
                        background: transparent;
                        border: none;
                        padding: 0.5rem;
                        cursor: pointer;
                    ">✕</button>
                `;

                document.body.appendChild(toast);
            }
        }

        // Função para instalar o PWA
        async function installPWA() {
            if (deferredPrompt) {
                try {
                    // Mostra o prompt de instalação
                    deferredPrompt.prompt();
                    // Espera o usuário responder ao prompt
                    const choiceResult = await deferredPrompt.userChoice;
                    if (choiceResult.outcome === 'accepted') {
                        console.log('Usuário aceitou instalar o PWA');
                        // Após instalar, sincroniza os dados offline
                        syncOfflineData();
                    } else {
                        console.log('Usuário recusou instalar o PWA');
                    }
                    // Limpa o prompt salvo, só pode ser usado uma vez
                    deferredPrompt = null;
                    // Remove o toast
                    document.getElementById('pwa-install-toast')?.remove();
                } catch (error) {
                    console.error('Erro ao instalar PWA:', error);
                }
            }
        }

        // Função para sincronizar dados offline
        async function syncOfflineData() {
            try {
                const response = await fetch('/api/data/offline', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const data = await response.json();
                    localStorage.setItem('offline_data', JSON.stringify(data));
                    console.log('Dados offline sincronizados com sucesso');
                } else {
                    console.error('Erro ao sincronizar dados offline');
                }
            } catch (error) {
                console.error('Erro ao sincronizar dados offline:', error);
            }
        }

        // Sincroniza dados quando ficar online
        window.addEventListener('online', syncOfflineData);

        // Sincroniza dados na carga inicial se estiver online
        if (navigator.onLine) {
            syncOfflineData();
        }
    </script>
    <script>
        syncPendingInspections();

        window.addEventListener('online', syncPendingInspections);

        async function syncPendingInspections() {
            const queue = JSON.parse(localStorage.getItem('pending_inspections')) || [];
            if (queue.length === 0) {
                return;
            }

            console.log(`Sincronizando ${queue.length} inspeções...`);
            let failedQueue = [];

            for (const inspecao of queue) {
                try {
                    const response = await fetch("{{ route('api.inspecoes.sync') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(inspecao)
                    });

                    if (response.ok) {
                        console.log(`Inspeção ${inspecao.offline_id} sincronizada!`);
                    } else {
                        console.warn(`Falha ao sincronizar ${inspecao.offline_id}`);
                        failedQueue.push(inspecao);
                    }
                } catch (error) {
                    console.error('Erro de rede durante o sync:', error);
                    failedQueue.push(inspecao);
                }
            }

            localStorage.setItem('pending_inspections', JSON.stringify(failedQueue));

            if (failedQueue.length === 0 && queue.length > 0) {
                alert('Todas as inspeções pendentes foram sincronizadas com o servidor!');
                window.location.reload();
            }
        }
    </script>
</body>

</html>
