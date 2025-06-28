// VidaduAcademy Service Worker
const CACHE_NAME = 'vidadu-academy-v1.0.0'
const STATIC_CACHE_NAME = 'vidadu-static-v1.0.0'
const DYNAMIC_CACHE_NAME = 'vidadu-dynamic-v1.0.0'

// Cache static assets
const STATIC_ASSETS = [
  '/',
  '/manifest.json',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png',
  // Add critical CSS and JS files
]

// Cache API endpoints
const API_CACHE_PATTERNS = [
  /\/api\/courses/,
  /\/api\/auth\/user/,
]

// Don't cache these patterns
const CACHE_BLACKLIST = [
  /\/api\/auth\/login/,
  /\/api\/auth\/logout/,
  /\/api\/payments/,
  /\/admin/,
]

// Install event - cache static assets
self.addEventListener('install', (event) => {
  console.log('Service Worker installing...')
  
  event.waitUntil(
    caches.open(STATIC_CACHE_NAME)
      .then((cache) => {
        console.log('Caching static assets')
        return cache.addAll(STATIC_ASSETS)
      })
      .then(() => {
        return self.skipWaiting()
      })
      .catch((error) => {
        console.error('Failed to cache static assets:', error)
      })
  )
})

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  console.log('Service Worker activating...')
  
  event.waitUntil(
    caches.keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => {
            if (cacheName !== STATIC_CACHE_NAME && 
                cacheName !== DYNAMIC_CACHE_NAME && 
                cacheName !== CACHE_NAME) {
              console.log('Deleting old cache:', cacheName)
              return caches.delete(cacheName)
            }
          })
        )
      })
      .then(() => {
        return self.clients.claim()
      })
  )
})

// Fetch event - implement caching strategies
self.addEventListener('fetch', (event) => {
  const { request } = event
  const url = new URL(request.url)

  // Skip non-GET requests and blacklisted URLs
  if (request.method !== 'GET' || 
      CACHE_BLACKLIST.some(pattern => pattern.test(url.pathname))) {
    return
  }

  // Handle different types of requests
  if (request.destination === 'document') {
    // HTML pages - Network first, fallback to cache
    event.respondWith(networkFirstStrategy(request))
  } else if (API_CACHE_PATTERNS.some(pattern => pattern.test(url.pathname))) {
    // API requests - Stale while revalidate
    event.respondWith(staleWhileRevalidateStrategy(request))
  } else if (request.destination === 'image') {
    // Images - Cache first
    event.respondWith(cacheFirstStrategy(request))
  } else {
    // Other assets - Stale while revalidate
    event.respondWith(staleWhileRevalidateStrategy(request))
  }
})

// Network First Strategy (for HTML pages)
async function networkFirstStrategy(request) {
  try {
    const networkResponse = await fetch(request)
    const cache = await caches.open(DYNAMIC_CACHE_NAME)
    cache.put(request, networkResponse.clone())
    return networkResponse
  } catch (error) {
    console.log('Network failed, trying cache:', error)
    const cachedResponse = await caches.match(request)
    if (cachedResponse) {
      return cachedResponse
    }
    // Return offline page if available
    return caches.match('/offline.html')
  }
}

// Cache First Strategy (for images and static assets)
async function cacheFirstStrategy(request) {
  const cachedResponse = await caches.match(request)
  if (cachedResponse) {
    return cachedResponse
  }

  try {
    const networkResponse = await fetch(request)
    const cache = await caches.open(DYNAMIC_CACHE_NAME)
    cache.put(request, networkResponse.clone())
    return networkResponse
  } catch (error) {
    console.log('Failed to fetch from network:', error)
    // Return fallback image if needed
    if (request.destination === 'image') {
      return caches.match('/images/placeholder.jpg')
    }
    throw error
  }
}

// Stale While Revalidate Strategy (for API and other assets)
async function staleWhileRevalidateStrategy(request) {
  const cache = await caches.open(DYNAMIC_CACHE_NAME)
  const cachedResponse = await cache.match(request)
  
  // Fetch from network and update cache in background
  const networkResponsePromise = fetch(request)
    .then((response) => {
      cache.put(request, response.clone())
      return response
    })
    .catch((error) => {
      console.log('Network request failed:', error)
    })

  // Return cached response immediately if available, otherwise wait for network
  return cachedResponse || networkResponsePromise
}

// Background sync for offline actions
self.addEventListener('sync', (event) => {
  console.log('Background sync event:', event.tag)
  
  if (event.tag === 'course-enrollment') {
    event.waitUntil(syncCourseEnrollments())
  }
  
  if (event.tag === 'course-progress') {
    event.waitUntil(syncCourseProgress())
  }
})

// Sync offline course enrollments
async function syncCourseEnrollments() {
  try {
    // Get offline enrollments from IndexedDB
    const offlineEnrollments = await getOfflineEnrollments()
    
    for (const enrollment of offlineEnrollments) {
      try {
        await fetch('/api/enrollments', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(enrollment)
        })
        
        // Remove from offline storage after successful sync
        await removeOfflineEnrollment(enrollment.id)
      } catch (error) {
        console.error('Failed to sync enrollment:', error)
      }
    }
  } catch (error) {
    console.error('Background sync failed:', error)
  }
}

// Sync course progress
async function syncCourseProgress() {
  try {
    // Similar implementation for course progress
    const offlineProgress = await getOfflineProgress()
    
    for (const progress of offlineProgress) {
      try {
        await fetch('/api/course-progress', {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(progress)
        })
        
        await removeOfflineProgress(progress.id)
      } catch (error) {
        console.error('Failed to sync progress:', error)
      }
    }
  } catch (error) {
    console.error('Progress sync failed:', error)
  }
}

// Push notifications
self.addEventListener('push', (event) => {
  console.log('Push notification received:', event)
  
  const options = {
    body: 'Máte nové oznámenie od VidaduAcademy',
    icon: '/icons/icon-192x192.png',
    badge: '/icons/badge.png',
    tag: 'vidadu-notification',
    renotify: true,
    requireInteraction: false,
    actions: [
      {
        action: 'open',
        title: 'Otvoriť',
        icon: '/icons/action-open.png'
      },
      {
        action: 'dismiss',
        title: 'Zavrieť',
        icon: '/icons/action-dismiss.png'
      }
    ]
  }

  if (event.data) {
    const data = event.data.json()
    options.body = data.body || options.body
    options.title = data.title || 'VidaduAcademy'
    options.url = data.url || '/'
  }

  event.waitUntil(
    self.registration.showNotification(options.title || 'VidaduAcademy', options)
  )
})

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
  console.log('Notification click received:', event)
  
  event.notification.close()
  
  if (event.action === 'dismiss') {
    return
  }
  
  const url = event.notification.data?.url || '/'
  
  event.waitUntil(
    clients.matchAll({ type: 'window' })
      .then((clientList) => {
        // Try to focus existing window
        for (const client of clientList) {
          if (client.url === url && 'focus' in client) {
            return client.focus()
          }
        }
        
        // Open new window
        if (clients.openWindow) {
          return clients.openWindow(url)
        }
      })
  )
})

// Placeholder functions for IndexedDB operations
async function getOfflineEnrollments() {
  // Implementation for getting offline enrollments from IndexedDB
  return []
}

async function removeOfflineEnrollment(id) {
  // Implementation for removing offline enrollment
}

async function getOfflineProgress() {
  // Implementation for getting offline progress from IndexedDB
  return []
}

async function removeOfflineProgress(id) {
  // Implementation for removing offline progress
}

// Update check
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting()
  }
})
