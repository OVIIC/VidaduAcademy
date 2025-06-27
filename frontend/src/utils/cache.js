// Simple in-memory cache with TTL
class ApiCache {
  constructor() {
    this.cache = new Map()
    this.defaultTTL = 5 * 60 * 1000 // 5 minutes
  }

  generateKey(url, params = {}) {
    const paramString = JSON.stringify(params)
    return `${url}:${paramString}`
  }

  set(key, data, ttl = this.defaultTTL) {
    const expiry = Date.now() + ttl
    this.cache.set(key, { data, expiry })
  }

  get(key) {
    const item = this.cache.get(key)
    
    if (!item) {
      return null
    }

    if (Date.now() > item.expiry) {
      this.cache.delete(key)
      return null
    }

    return item.data
  }

  has(key) {
    const item = this.cache.get(key)
    
    if (!item) {
      return false
    }

    if (Date.now() > item.expiry) {
      this.cache.delete(key)
      return false
    }

    return true
  }

  clear() {
    this.cache.clear()
  }

  // Remove expired entries
  cleanup() {
    const now = Date.now()
    for (const [key, item] of this.cache.entries()) {
      if (now > item.expiry) {
        this.cache.delete(key)
      }
    }
  }
}

// Create global cache instance
export const apiCache = new ApiCache()

// Auto cleanup every 10 minutes
setInterval(() => {
  apiCache.cleanup()
}, 10 * 60 * 1000)

export default apiCache
