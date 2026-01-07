import { ref, computed, watch } from 'vue'

// Debounce utility for performance optimization
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

// Throttle utility for scroll events
export function throttle(func, limit) {
  let inThrottle
  
  return function(...args) {
    if (!inThrottle) {
      func.apply(this, args)
      inThrottle = true
      setTimeout(() => inThrottle = false, limit)
    }
  }
}

// Optimize component for large lists
export function useVirtualScrolling(items, itemHeight, containerHeight) {
  const visibleItems = ref([])
  const scrollTop = ref(0)
  
  const startIndex = computed(() => 
    Math.floor(scrollTop.value / itemHeight)
  )
  
  const endIndex = computed(() => 
    Math.min(
      startIndex.value + Math.ceil(containerHeight / itemHeight) + 1,
      items.value.length - 1
    )
  )
  
  const offsetY = computed(() => startIndex.value * itemHeight)
  
  watch([startIndex, endIndex], () => {
    visibleItems.value = items.value.slice(
      startIndex.value, 
      endIndex.value + 1
    )
  }, { immediate: true })
  
  return {
    visibleItems,
    scrollTop,
    offsetY,
    totalHeight: computed(() => items.value.length * itemHeight)
  }
}

export default {
  debounce,
  throttle,
  useVirtualScrolling
}
