// Performance monitoring utilities
class PerformanceMonitor {
  constructor() {
    this.metrics = new Map()
    this.observers = new Map()
    this.isEnabled = import.meta.env.MODE === 'production'
  }

  // Start timing a operation
  startTiming(label) {
    if (!this.isEnabled) return
    
    this.metrics.set(label, {
      startTime: performance.now(),
      label
    })
  }

  // End timing and log result
  endTiming(label) {
    if (!this.isEnabled) return
    
    const metric = this.metrics.get(label)
    if (!metric) {
      console.warn(`No timing started for label: ${label}`)
      return
    }

    const duration = performance.now() - metric.startTime
    console.log(`⏱️ ${label}: ${duration.toFixed(2)}ms`)
    
    // Send to analytics if needed
    this.sendMetric(label, duration)
    
    this.metrics.delete(label)
    return duration
  }

  // Measure function execution time
  async measureAsync(label, fn) {
    this.startTiming(label)
    try {
      const result = await fn()
      this.endTiming(label)
      return result
    } catch (error) {
      this.endTiming(label)
      throw error
    }
  }

  // Measure sync function execution time
  measure(label, fn) {
    this.startTiming(label)
    try {
      const result = fn()
      this.endTiming(label)
      return result
    } catch (error) {
      this.endTiming(label)
      throw error
    }
  }

  // Monitor network requests
  monitorFetch(url, options = {}) {
    if (!this.isEnabled) return fetch(url, options)
    
    const label = `API: ${url.split('/').pop()}`
    this.startTiming(label)
    
    return fetch(url, options).finally(() => {
      this.endTiming(label)
    })
  }

  // Monitor component render times
  setupRenderMonitoring() {
    if (!this.isEnabled || typeof window === 'undefined') return

    // Monitor LCP (Largest Contentful Paint)
    if ('PerformanceObserver' in window) {
      const lcpObserver = new PerformanceObserver((list) => {
        const entries = list.getEntries()
        const lastEntry = entries[entries.length - 1]
        console.log(`🎨 LCP: ${lastEntry.startTime.toFixed(2)}ms`)
        this.sendMetric('LCP', lastEntry.startTime)
      })
      
      try {
        lcpObserver.observe({ entryTypes: ['largest-contentful-paint'] })
        this.observers.set('lcp', lcpObserver)
      } catch (e) {
        console.warn('LCP monitoring not supported')
      }

      // Monitor FID (First Input Delay)
      const fidObserver = new PerformanceObserver((list) => {
        const entries = list.getEntries()
        entries.forEach((entry) => {
          console.log(`👆 FID: ${entry.processingStart - entry.startTime}ms`)
          this.sendMetric('FID', entry.processingStart - entry.startTime)
        })
      })
      
      try {
        fidObserver.observe({ entryTypes: ['first-input'] })
        this.observers.set('fid', fidObserver)
      } catch (e) {
        console.warn('FID monitoring not supported')
      }

      // Monitor CLS (Cumulative Layout Shift)
      let clsValue = 0
      const clsObserver = new PerformanceObserver((list) => {
        const entries = list.getEntries()
        entries.forEach((entry) => {
          if (!entry.hadRecentInput) {
            clsValue += entry.value
          }
        })
        console.log(`📐 CLS: ${clsValue.toFixed(4)}`)
        this.sendMetric('CLS', clsValue)
      })
      
      try {
        clsObserver.observe({ entryTypes: ['layout-shift'] })
        this.observers.set('cls', clsObserver)
      } catch (e) {
        console.warn('CLS monitoring not supported')
      }
    }
  }

  // Monitor memory usage
  getMemoryInfo() {
    if (!this.isEnabled || !performance.memory) return null
    
    const memory = performance.memory
    return {
      used: Math.round(memory.usedJSHeapSize / 1024 / 1024),
      total: Math.round(memory.totalJSHeapSize / 1024 / 1024),
      limit: Math.round(memory.jsHeapSizeLimit / 1024 / 1024)
    }
  }

  // Log memory usage
  logMemory(label = 'Memory Usage') {
    if (!this.isEnabled) return
    
    const memory = this.getMemoryInfo()
    if (memory) {
      console.log(`💾 ${label}: ${memory.used}MB / ${memory.total}MB (limit: ${memory.limit}MB)`)
    }
  }

  // Send metrics to analytics service
  sendMetric(name, value, tags = {}) {
    if (!this.isEnabled) return
    
    // Here you could send to analytics service like Google Analytics, Mixpanel, etc.
    // For now, just store locally for debugging
    const metrics = JSON.parse(localStorage.getItem('performance_metrics') || '[]')
    metrics.push({
      name,
      value,
      tags,
      timestamp: Date.now(),
      url: window.location.pathname
    })
    
    // Keep only last 100 metrics
    if (metrics.length > 100) {
      metrics.splice(0, metrics.length - 100)
    }
    
    localStorage.setItem('performance_metrics', JSON.stringify(metrics))
  }

  // Get stored performance metrics
  getMetrics() {
    return JSON.parse(localStorage.getItem('performance_metrics') || '[]')
  }

  // Clear stored metrics
  clearMetrics() {
    localStorage.removeItem('performance_metrics')
  }

  // Cleanup observers
  cleanup() {
    this.observers.forEach(observer => observer.disconnect())
    this.observers.clear()
    this.metrics.clear()
  }
}

// Create singleton instance
const performanceMonitor = new PerformanceMonitor()

// Auto-setup monitoring when module loads
if (typeof window !== 'undefined') {
  performanceMonitor.setupRenderMonitoring()
  
  // Cleanup on page unload
  window.addEventListener('beforeunload', () => {
    performanceMonitor.cleanup()
  })
}

// Vue composable for performance monitoring
export function usePerformance() {
  const startTiming = (label) => performanceMonitor.startTiming(label)
  const endTiming = (label) => performanceMonitor.endTiming(label)
  const measureAsync = (label, fn) => performanceMonitor.measureAsync(label, fn)
  const measure = (label, fn) => performanceMonitor.measure(label, fn)
  const logMemory = (label) => performanceMonitor.logMemory(label)
  const getMetrics = () => performanceMonitor.getMetrics()
  const clearMetrics = () => performanceMonitor.clearMetrics()

  return {
    startTiming,
    endTiming,
    measureAsync,
    measure,
    logMemory,
    getMetrics,
    clearMetrics
  }
}

export default performanceMonitor
