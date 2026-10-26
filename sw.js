const CACHE_VERSION = "qieos-v6";
const APP_SHELL_CACHE = `${CACHE_VERSION}-shell`;
const RUNTIME_CACHE = `${CACHE_VERSION}-runtime`;

const BASE = "/qieos";

const APP_SHELL = [
    `${BASE}/manifest.json`,
    `${BASE}/css/volt.css`,
    `${BASE}/assets/js/volt.js`,
    `${BASE}/assets/img/brand/qieos.png`,
    `${BASE}/assets/img/brand/qieos2.png`,
    `${BASE}/assets/img/brand/icon-192.png`,
    `${BASE}/assets/img/brand/icon-512.png`,
    `${BASE}/offline.html`
];

self.addEventListener("install", event => {
    event.waitUntil(
        caches.open(APP_SHELL_CACHE).then(cache =>
            Promise.allSettled(
                APP_SHELL.map(url =>
                    cache.add(new Request(url, { cache: "reload" })).catch(()=>{})
                )
            )
        )
    );
    self.skipWaiting();
});

self.addEventListener("activate", event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys
                    .filter(key => !key.startsWith(CACHE_VERSION))
                    .map(key => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    );
});

function isStaticAsset(url) {
    return /\.(css|js|png|jpg|jpeg|gif|svg|webp|ico|woff2?|ttf|eot)$/i.test(url.pathname);
}

self.addEventListener("fetch", event => {
    const { request } = event;
    if (request.method !== "GET") return;

    const url = new URL(request.url);
    if (url.origin !== self.location.origin) return;

    // Skip semua PHP pages & API — biarkan browser handle langsung
    if (url.pathname.endsWith('.php') || url.pathname.indexOf('-search.php') !== -1 || url.pathname.indexOf('-action.php') !== -1) return;

    // Hanya cache static assets
    if (isStaticAsset(url)) {
        event.respondWith(
            caches.match(request).then(cached => {
                const fetched = fetch(request).then(response => {
                    if (response && response.status === 200 && response.type === "basic") {
                        const copy = response.clone();
                        caches.open(RUNTIME_CACHE).then(cache => cache.put(request, copy));
                    }
                    return response;
                }).catch(() => cached);
                return cached || fetched;
            })
        );
    }
    // Semua request lain → biarkan browser handle (tidak intercept)
});

// Klik notifikasi chat → fokus tab yang ada atau buka tab baru ke percakapan
self.addEventListener("notificationclick", event => {
    event.notification.close();

    const data = event.notification.data || {};
    const target = data.url || `${BASE}/pages/chat/chat.php`;

    event.waitUntil(
        self.clients.matchAll({ type: "window", includeUncontrolled: true }).then(clientsArr => {
            for (const client of clientsArr) {
                if (client.url.indexOf(self.location.origin) === 0 && "focus" in client) {
                    if ("navigate" in client) {
                        client.navigate(target).catch(() => {});
                    }
                    return client.focus();
                }
            }
            if (self.clients.openWindow) return self.clients.openWindow(target);
        })
    );
});
