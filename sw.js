const CACHE_NAME = 'capiznon-geo-v1';
const OFFLINE_URLS = [
  '/',
];

// Install event - cache offline URLs
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(OFFLINE_URLS))
  );
  self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
    )
  );
  self.clients.claim();
});

// Fetch event - serve from cache when offline
self.addEventListener('fetch', event => {
  const req = event.request;
  if (req.method !== 'GET') return;

  // Skip non-HTTP requests
  if (!req.url.startsWith('http')) return;

  event.respondWith(
    caches.match(req).then(cached => {
      if (cached) return cached;
      
      // Try network first, fallback to cache
      return fetch(req).then(response => {
        // Only cache successful responses
        if (!response || response.status !== 200 || response.type !== 'basic') {
          return response;
        }
        
        // Clone response since it can only be consumed once
        const responseToCache = response.clone();
        
        caches.open(CACHE_NAME).then(cache => {
          cache.put(req, responseToCache);
        });
        
        return response;
      }).catch(() => {
        // If network fails, try to serve from cache or offline page
        return caches.match('/').then(cached => {
          if (cached) return cached;
          
          // Return a basic offline response
          return new Response('Offline', {
            status: 503,
            statusText: 'Service Unavailable'
          });
        });
      });
    })
  );
});

// Handle messages from clients
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});
