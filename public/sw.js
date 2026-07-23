const CACHE_NAME = 'book-management-v1';
const ASSETS_TO_CACHE = [
    '/',
    '/books',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(ASSETS_TO_CACHE);
        })
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            );
        })
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    if (url.pathname.startsWith('/storage/books/')) {
        event.respondWith(
            caches.open(CACHE_NAME).then((cache) => {
                return cache.match(request).then((cached) => {
                    const fetchPromise = fetch(request).then((response) => {
                        if (response.ok) {
                            const cloned = response.clone();
                            cache.put(request, cloned);
                        }
                        return response;
                    }).catch(() => cached);
                    return cached || fetchPromise;
                });
            })
        );
        return;
    }

    event.respondWith(
        caches.match(request).then((cached) => {
            const fetchPromise = fetch(request).then((response) => {
                if (response.ok && request.method === 'GET' && response.status === 200) {
                    const cloned = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(request, cloned);
                    });
                }
                return response;
            }).catch(() => cached);

            return cached || fetchPromise;
        })
    );
});
