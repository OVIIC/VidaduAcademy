/**
 * Utility functions for better UX and performance
 */

/**
 * Debounce function to limit function calls
 * @param {Function} func - Function to debounce
 * @param {number} wait - Wait time in milliseconds
 * @param {boolean} immediate - Execute immediately on first call
 * @returns {Function} Debounced function
 */
export function debounce(func, wait, immediate = false) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      timeout = null
      if (!immediate) func(...args)
    }
    const callNow = immediate && !timeout
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
    if (callNow) func(...args)
  }
}

/**
 * Throttle function to limit function execution rate
 * @param {Function} func - Function to throttle
 * @param {number} limit - Limit in milliseconds
 * @returns {Function} Throttled function
 */
export function throttle(func, limit) {
  let inThrottle
  return function executedFunction(...args) {
    if (!inThrottle) {
      func.apply(this, args)
      inThrottle = true
      setTimeout(() => inThrottle = false, limit)
    }
  }
}

/**
 * Format file size in human readable format
 * @param {number} bytes - Size in bytes
 * @returns {string} Formatted size
 */
export function formatFileSize(bytes) {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

/**
 * Format duration in minutes to human readable format
 * @param {number} minutes - Duration in minutes
 * @returns {string} Formatted duration
 */
export function formatDuration(minutes) {
  if (!minutes) return '0 min'
  
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  
  if (hours === 0) {
    return `${mins} min`
  }
  
  if (mins === 0) {
    return `${hours}h`
  }
  
  return `${hours}h ${mins}min`
}

/**
 * Format number to human readable format (1K, 1M, etc.)
 * @param {number} num - Number to format
 * @returns {string} Formatted number
 */
export function formatNumber(num) {
  if (num < 1000) return num.toString()
  if (num < 1000000) return (num / 1000).toFixed(1) + 'K'
  if (num < 1000000000) return (num / 1000000).toFixed(1) + 'M'
  return (num / 1000000000).toFixed(1) + 'B'
}

/**
 * Format date to relative time (e.g., "2 days ago")
 * @param {string|Date} date - Date to format
 * @returns {string} Relative time string
 */
export function formatRelativeTime(date) {
  const now = new Date()
  const targetDate = new Date(date)
  const diffInSeconds = Math.floor((now - targetDate) / 1000)
  
  const intervals = {
    year: 31536000,
    month: 2592000,
    week: 604800,
    day: 86400,
    hour: 3600,
    minute: 60
  }
  
  for (const [unit, seconds] of Object.entries(intervals)) {
    const interval = Math.floor(diffInSeconds / seconds)
    if (interval >= 1) {
      return `pred ${interval} ${getUnitLabel(unit, interval)}`
    }
  }
  
  return 'práve teraz'
}

/**
 * Get Slovak unit label with proper grammar
 * @param {string} unit - Time unit
 * @param {number} count - Count
 * @returns {string} Properly formatted unit
 */
function getUnitLabel(unit, count) {
  const labels = {
    year: ['rokom', 'rokmi', 'rokmi'],
    month: ['mesiacom', 'mesiacmi', 'mesiacmi'],
    week: ['týždňom', 'týždňami', 'týždňami'],
    day: ['dňom', 'dňami', 'dňami'],
    hour: ['hodinou', 'hodinami', 'hodinami'],
    minute: ['minútou', 'minútami', 'minútami']
  }
  
  if (count === 1) return labels[unit][0]
  if (count >= 2 && count <= 4) return labels[unit][1]
  return labels[unit][2]
}

/**
 * Generate initials from name
 * @param {string} name - Full name
 * @returns {string} Initials
 */
export function getInitials(name) {
  if (!name) return '?'
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .substring(0, 2)
}

/**
 * Validate email format
 * @param {string} email - Email to validate
 * @returns {boolean} Is valid email
 */
export function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

/**
 * Generate random ID
 * @param {number} length - ID length
 * @returns {string} Random ID
 */
export function generateId(length = 8) {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
  let result = ''
  for (let i = 0; i < length; i++) {
    result += chars.charAt(Math.floor(Math.random() * chars.length))
  }
  return result
}

/**
 * Device and viewport detection utilities
 */

/**
 * Check if device is mobile
 * @returns {boolean} True if mobile device
 */
export function isMobileDevice() {
  return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
}

/**
 * Check if device is tablet
 * @returns {boolean} True if tablet device
 */
export function isTabletDevice() {
  return /iPad|Android|tablet/i.test(navigator.userAgent) && !isMobileDevice()
}

/**
 * Check if device is desktop
 * @returns {boolean} True if desktop device
 */
export function isDesktopDevice() {
  return !isMobileDevice() && !isTabletDevice()
}

/**
 * Get viewport dimensions
 * @returns {Object} Viewport width and height
 */
export function getViewportSize() {
  return {
    width: Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0),
    height: Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0)
  }
}

/**
 * Check if viewport is mobile size
 * @returns {boolean} True if mobile viewport
 */
export function isMobileViewport() {
  return getViewportSize().width < 640
}

/**
 * Check if viewport is tablet size
 * @returns {boolean} True if tablet viewport
 */
export function isTabletViewport() {
  const width = getViewportSize().width
  return width >= 640 && width < 1024
}

/**
 * Check if viewport is desktop size
 * @returns {boolean} True if desktop viewport
 */
export function isDesktopViewport() {
  return getViewportSize().width >= 1024
}

/**
 * Performance and user experience utilities
 */

/**
 * Intersection Observer utility for lazy loading
 * @param {Element} element - Element to observe
 * @param {Function} callback - Callback when element enters viewport
 * @param {Object} options - Intersection observer options
 * @returns {IntersectionObserver} Observer instance
 */
export function createIntersectionObserver(element, callback, options = {}) {
  const defaultOptions = {
    root: null,
    rootMargin: '50px',
    threshold: 0.1
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        callback(entry)
        observer.unobserve(entry.target)
      }
    })
  }, { ...defaultOptions, ...options })

  if (element) {
    observer.observe(element)
  }

  return observer
}

/**
 * Smooth scroll to element
 * @param {string|Element} target - CSS selector or element
 * @param {Object} options - Scroll options
 */
export function smoothScrollTo(target, options = {}) {
  const element = typeof target === 'string' ? document.querySelector(target) : target
  
  if (element) {
    element.scrollIntoView({
      behavior: 'smooth',
      block: options.block || 'start',
      inline: options.inline || 'nearest'
    })
  }
}

/**
 * Generate responsive image srcset
 * @param {string} basePath - Base image path
 * @param {Array} sizes - Array of sizes
 * @returns {string} Srcset string
 */
export function generateResponsiveSrcset(basePath, sizes = [320, 640, 768, 1024, 1280]) {
  return sizes.map(size => {
    const path = basePath.replace(/(\.[^.]+)$/, `_${size}w$1`)
    return `${path} ${size}w`
  }).join(', ')
}

/**
 * Touch and gesture utilities
 */

/**
 * Handle touch events for swipe gestures
 * @param {Element} element - Element to attach listeners
 * @param {Object} callbacks - Swipe callbacks
 * @returns {Function} Cleanup function
 */
export function setupSwipeGestures(element, callbacks = {}) {
  let startX = 0
  let startY = 0
  let distX = 0
  let distY = 0
  const threshold = 100
  const restraint = 100

  const handleTouchStart = (e) => {
    const touch = e.touches[0]
    startX = touch.clientX
    startY = touch.clientY
  }

  const handleTouchEnd = (e) => {
    const touch = e.changedTouches[0]
    distX = touch.clientX - startX
    distY = touch.clientY - startY

    if (Math.abs(distX) >= threshold && Math.abs(distY) <= restraint) {
      if (distX > 0 && callbacks.onSwipeRight) {
        callbacks.onSwipeRight()
      } else if (distX < 0 && callbacks.onSwipeLeft) {
        callbacks.onSwipeLeft()
      }
    }

    if (Math.abs(distY) >= threshold && Math.abs(distX) <= restraint) {
      if (distY > 0 && callbacks.onSwipeDown) {
        callbacks.onSwipeDown()
      } else if (distY < 0 && callbacks.onSwipeUp) {
        callbacks.onSwipeUp()
      }
    }
  }

  element.addEventListener('touchstart', handleTouchStart, { passive: true })
  element.addEventListener('touchend', handleTouchEnd, { passive: true })

  return () => {
    element.removeEventListener('touchstart', handleTouchStart)
    element.removeEventListener('touchend', handleTouchEnd)
  }
}

/**
 * Local storage utilities with error handling
 */

/**
 * Safe localStorage setter
 * @param {string} key - Storage key
 * @param {any} value - Value to store
 * @returns {boolean} Success status
 */
export function setLocalStorageItem(key, value) {
  try {
    localStorage.setItem(key, JSON.stringify(value))
    return true
  } catch (error) {
    console.warn('Failed to save to localStorage:', error)
    return false
  }
}

/**
 * Safe localStorage getter
 * @param {string} key - Storage key
 * @param {any} defaultValue - Default value if not found
 * @returns {any} Stored value or default
 */
export function getLocalStorageItem(key, defaultValue = null) {
  try {
    const item = localStorage.getItem(key)
    return item ? JSON.parse(item) : defaultValue
  } catch (error) {
    console.warn('Failed to read from localStorage:', error)
    return defaultValue
  }
}

/**
 * Remove localStorage item
 * @param {string} key - Storage key
 * @returns {boolean} Success status
 */
export function removeLocalStorageItem(key) {
  try {
    localStorage.removeItem(key)
    return true
  } catch (error) {
    console.warn('Failed to remove from localStorage:', error)
    return false
  }
}

/**
 * Retry function with exponential backoff
 * @param {Function} fn - Function to retry
 * @param {number} retries - Number of retries
 * @param {number} baseDelay - Base delay in ms
 * @returns {Promise} Promise that resolves with function result
 */
export async function retryWithBackoff(fn, retries = 3, baseDelay = 1000) {
  for (let i = 0; i < retries; i++) {
    try {
      return await fn()
    } catch (error) {
      if (i === retries - 1) throw error
      await new Promise(resolve => setTimeout(resolve, baseDelay * Math.pow(2, i)))
    }
  }
}
