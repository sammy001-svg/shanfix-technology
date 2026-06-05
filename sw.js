const CACHE_NAME = 'shanfix-v1';

// Static assets to pre-cache on install
const PRECACHE_ASSETS = [
  '/',
  '/index.css',
  '/main.js',
  '/favicon.svg',
  '/assets/shanfix-logo.png',
  '/assets/icons/icon-192x192.png',
  '/assets/icons/icon-512x512.png',
  '/manifest.json',
];

// Cache-first assets (fonts, images, icons)
const CACHE_FIRST_PATTERNS = [
  /\/assets\//,
  /fonts\.googleapis\.com/,
  /fonts\.gstatic\.com/,
  /cdnjs\.cloudflare\.com/,
  /unpkg\.com/,
];

// Network-first (dynamic PHP pages, API calls)
const NETWORK_FIRST_PATTERNS = [
  /\.php(\?.*)?$/,
  /\/api\//,
  /\/admin\//,
  /\/client\//,
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_ASSETS))
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k)))
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Skip non-GET and cross-origin requests except known CDNs
  if (request.method !== 'GET') return;

  // Network-first for PHP pages and API
  if (NETWORK_FIRST_PATTERNS.some((p) => p.test(url.pathname + url.search))) {
    event.respondWith(
      fetch(request)
        .then((response) => {
          if (response.ok) {
            const clone = response.clone();
            caches.open(CACHE_NAME).then((c) => c.put(request, clone));
          }
          return response;
        })
        .catch(() => caches.match(request))
    );
    return;
  }

  // Cache-first for static assets and CDN resources
  if (CACHE_FIRST_PATTERNS.some((p) => p.test(request.url))) {
    event.respondWith(
      caches.match(request).then(
        (cached) => cached || fetch(request).then((response) => {
          if (response.ok) {
            const clone = response.clone();
            caches.open(CACHE_NAME).then((c) => c.put(request, clone));
          }
          return response;
        })
      )
    );
    return;
  }

  // Default: network with cache fallback
  event.respondWith(
    fetch(request).catch(() => caches.match(request))
  );
});
