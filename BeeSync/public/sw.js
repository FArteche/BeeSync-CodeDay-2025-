const CACHE_NAME = 'beesync-v2';
const STATIC_CACHE = 'beesync-static-v2';
const DYNAMIC_CACHE = 'beesync-dynamic-v2';

// Assets que devem ser cacheados para funcionar offline
// Rotas que devem funcionar offline
const OFFLINE_ROUTES = [
    '/dashboard',
    '/apiarios',
    new RegExp('/apiarios/\\d+$'),  // matches /apiarios/{id}
    new RegExp('/colmeias/\\d+$'),  // matches /colmeias/{id}
    new RegExp('/inspecoes/create/\\d+$')  // matches /inspecoes/create/{id}
];

// Assets estáticos que devem ser cacheados
const STATIC_ASSETS = [
    '/',
    '/offline',
    '/manifest.json',
    '/images/SmallBee.png',
    '/images/NormalBee.png',
    '/images/imagebg.jpg'
];

// Instalação do Service Worker
self.addEventListener('install', event => {
    console.log('[Service Worker] Instalando...');
    event.waitUntil(
        Promise.all([
            // Cache de assets estáticos
            caches.open(STATIC_CACHE)
                .then(cache => {
                    console.log('[Service Worker] Pré-cacheando arquivos estáticos');
                    return cache.addAll(STATIC_ASSETS);
                }),
            // Cache de assets do Vite
            caches.open(VITE_CACHE)
                .then(cache => {
                    console.log('[Service Worker] Pré-cacheando assets do Vite');
                    return cache.addAll([
                        '/build/assets/app-CXDpL9bK.js',
                        '/build/assets/app-BIp1yz5s.css'
                    ]);
                })
        ])
    );
    self.skipWaiting();
});

// Ativação do Service Worker
// Cache separado para assets do Vite que têm hash no nome
const VITE_CACHE = 'beesync-vite-v1';

self.addEventListener('activate', event => {
    console.log('[Service Worker] Ativando...');
    event.waitUntil(
        caches.keys()
            .then(keyList => {
                return Promise.all(keyList.map(key => {
                    if (key !== STATIC_CACHE && key !== DYNAMIC_CACHE && key !== VITE_CACHE) {
                        console.log('[Service Worker] Removendo cache antigo:', key);
                        return caches.delete(key);
                    }
                }));
            })
    );
    return self.clients.claim();
});

// Função para verificar se a URL corresponde a alguma das rotas offline
function matchesOfflineRoute(url) {
    const pathname = new URL(url).pathname;
    return OFFLINE_ROUTES.some(route => {
        if (route instanceof RegExp) {
            return route.test(pathname);
        }
        return route === pathname;
    });
}

// Estratégia de Cache
self.addEventListener('fetch', event => {
    // Ignorar requisições não GET
    if (event.request.method !== 'GET') return;

    // Ignorar requisições para a API exceto GETs de dados essenciais
    if (event.request.url.includes('/api/') && !event.request.url.includes('/api/data/')) {
        return;
    }

    const url = new URL(event.request.url);
    const isNavigationRequest = event.request.mode === 'navigate';
    const isOfflineRoute = matchesOfflineRoute(event.request.url);
    const isStaticAsset = STATIC_ASSETS.some(asset => event.request.url.includes(asset));
    const isViteAsset = url.pathname.startsWith('/build/');

    if (isNavigationRequest || isOfflineRoute || isStaticAsset || isViteAsset) {
        event.respondWith(
            fetch(event.request)
                .then(response => {
                    // Se a resposta for válida, clonar e armazenar no cache apropriado
                    if (response.status === 200) {
                        const responseClone = response.clone();
                        const url = new URL(event.request.url);
                        const isViteAsset = url.pathname.startsWith('/build/');

                        const cacheName = isViteAsset ? VITE_CACHE : DYNAMIC_CACHE;
                        caches.open(cacheName)
                            .then(cache => {
                                // Armazena a resposta no cache
                                cache.put(event.request, responseClone);
                            });
                    }
                    return response;
                })
                .catch(async () => {
                    // Se offline, tentar pegar do cache
                    const cachedResponse = await caches.match(event.request);

                    if (cachedResponse) {
                        return cachedResponse;
                    }

                    // Para navegação HTML que não está no cache
                    if (isNavigationRequest) {
                        const cache = await caches.open(STATIC_CACHE);
                        return cache.match('/offline');
                    }

                    return null;
                })
        );
    }
});
