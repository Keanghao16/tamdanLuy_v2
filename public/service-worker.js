const CACHE_NAME = 'tamdan-luy-v4';
const ASSETS_TO_CACHE = [
    '/manifest.json',
    '/offline.html',
    '/login.html',
    '/favicon.svg',
    '/icons/icon-180x180-v4.svg',
    '/icons/icon-192x192-v4.svg',
    '/icons/icon-512x512-v4.svg'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(ASSETS_TO_CACHE))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);
    
    // Always handle navigation requests
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .catch(() => {
                    // Offline - serve appropriate page
                    if (url.pathname.includes('/login') || url.pathname === '/' || url.pathname === '') {
                        return caches.match('/login.html');
                    }
                    const protectedRoutes = ['/dashboard', '/accounts', '/categories', '/transactions', '/budgets', '/reports'];
                    if (protectedRoutes.some(route => url.pathname.includes(route))) {
                        return caches.match('/offline.html');
                    }
                    return caches.match('/offline.html');
                })
                .catch(() => caches.match('/offline.html'))
        );
        return;
    }
    
    // Skip non-GET requests
    if (event.request.method !== 'GET') {
        return;
    }
    
    // Check if origin is allowed
    const isLocal = url.origin.includes(self.location.origin);
    const isCDN = ['cdnjs.cloudflare.com', 'cdn.tailwindcss.com', 'cdn.jsdelivr.net']
        .some(origin => url.origin.includes(origin));
    
    if (!isLocal && !isCDN) {
        return;
    }
    
    // Handle other requests with cache-first strategy
    event.respondWith(
        caches.match(event.request)
            .then((response) => {
                return response || fetch(event.request);
            })
    );
});

self.addEventListener('push', (event) => {
    const data = event.data.json();
    const options = {
        body: data.body,
        icon: '/icons/icon-192x192-v4.svg',
        badge: '/icons/icon-192x192-v4.svg',
        vibrate: [100, 50, 100],
        data: { url: data.url || '/' }
    };
    event.waitUntil(self.registration.showNotification(data.title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data.url));
});