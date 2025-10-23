<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center bg-black p-4 rounded-md">
            <h2 class="font-semibold text-xl text-white leading-tight">
                Página Inicial
            </h2>
            <a href="{{ route('reports.index') }}"
                class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-800 active:bg-yellow-900 focus:outline-none">
                Relatórios
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mt-6">
                        <div class="mt-6">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold leading-tight">Meus Apiários</h3>
                                <a href="{{ route('apiarios.create') }}"
                                    class="inline-flex items-center px-4 py-2 bg-black border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:bg-yellow-800 active:bg-yellow-900 focus:outline-none">
                                    Criar Novo Apiário
                                </a>
                            </div>
                            <ul class="mt-4 space-y-2">
                                @forelse ($apiariosProprios as $apiario)
                                    <li class="p-4 bg-yellow-200 shadow rounded-lg">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <a href="{{ route('apiarios.show', $apiario) }}"
                                                    class="font-bold text-black hover:underline">
                                                    {{ $apiario->nome }}
                                                </a>
                                                <p class="text-sm text-gray-600">{{ $apiario->localizacao }}</p>
                                            </div>
                                            <button
                                                onclick="cacheApiario({{ $apiario->id }}, '{{ $apiario->nome }}')"
                                                class="px-3 py-1 text-xs bg-black text-white rounded hover:bg-gray-700 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                <span>Baixar dados</span>
                                            </button>
                                        </div>
                                    </li>
                                @empty
                                    <p class="text-sm text-gray-500">Você ainda não cadastrou nenhum apiário.</p>
                                @endforelse
                            </ul>
                        </div>

                        <div class="mt-8">
                            <h3 class="text-lg font-semibold">Apiários Compartilhados Comigo</h3>
                            <ul class="mt-4 space-y-2">
                                @forelse ($apiariosCompartilhados as $apiario)
                                    <li class="p-4 bg-yellow-200 shadow rounded-lg">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <a href="{{ route('apiarios.show', $apiario) }}"
                                                    class="font-bold text-black hover:underline">
                                                    {{ $apiario->nome }}
                                                </a>
                                                <p class="text-sm text-gray-600">{{ $apiario->localizacao }}</p>
                                                <p class="text-xs text-gray-500 mt-1">Proprietário: {{ $apiario->user->name }}</p>
                                            </div>
                                            <button
                                                onclick="cacheApiario({{ $apiario->id }}, '{{ $apiario->nome }}')"
                                                class="px-3 py-1 text-xs bg-black text-white rounded hover:bg-gray-700 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                <span>Baixar dados</span>
                                            </button>
                                        </div>
                                    </li>
                                @empty
                                    <p class="text-sm text-gray-500">Nenhum apiário foi compartilhado com você.</p>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast de progresso do cache -->
    <div id="cache-toast" class="fixed bottom-4 right-4 bg-black text-white p-4 rounded-lg shadow-lg hidden">
        <div class="flex items-center gap-3">
            <div class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
            <span id="cache-message">Preparando cache offline...</span>
        </div>
    </div>

    <script>
        async function cacheApiario(apiarioId, apiarioName) {
            const toast = document.getElementById('cache-toast');
            const messageEl = document.getElementById('cache-message');

            try {
                // Mostrar toast
                toast.classList.remove('hidden');
                messageEl.textContent = `Preparando cache para "${apiarioName}"...`;

                // Buscar URLs para cache
                const response = await fetch(`/api/cache/apiario/${apiarioId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) throw new Error('Falha ao buscar URLs');

                const data = await response.json();
                const totalItems = data.urls.length + data.assets.length;
                let processedItems = 0;

                // Registrar o Service Worker se necessário
                if ('serviceWorker' in navigator) {
                    const registration = await navigator.serviceWorker.ready;
                    const cache = await caches.open('beesync-dynamic-v2');

                    // Cachear URLs
                    messageEl.textContent = `Cacheando páginas de "${apiarioName}"...`;
                    for (const url of data.urls) {
                        try {
                            const response = await fetch(url);
                            if (response.ok) {
                                await cache.put(url, response);
                            }
                            processedItems++;
                            messageEl.textContent = `Progresso: ${Math.round((processedItems/totalItems) * 100)}%`;
                        } catch (error) {
                            console.error(`Falha ao cachear ${url}:`, error);
                        }
                    }

                    // Cachear assets
                    const assetCache = await caches.open('beesync-vite-v1');
                    for (const asset of data.assets) {
                        try {
                            const response = await fetch(asset);
                            if (response.ok) {
                                await assetCache.put(asset, response);
                            }
                            processedItems++;
                            messageEl.textContent = `Progresso: ${Math.round((processedItems/totalItems) * 100)}%`;
                        } catch (error) {
                            console.error(`Falha ao cachear asset ${asset}:`, error);
                        }
                    }

                    messageEl.textContent = `Cache concluído para "${apiarioName}"!`;
                    setTimeout(() => {
                        toast.classList.add('hidden');
                    }, 3000);
                }
            } catch (error) {
                console.error('Erro ao preparar cache:', error);
                messageEl.textContent = 'Erro ao preparar cache offline.';
                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 3000);
            }
        }
    </script>
</x-app-layout>
