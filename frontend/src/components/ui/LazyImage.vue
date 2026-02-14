<template>
  <div 
    ref="imageContainer"
    class="relative overflow-hidden"
    :class="containerClass"
  >
    <!-- Loading placeholder -->
    <div 
      v-if="loading" 
      class="absolute inset-0 bg-dark-800 animate-pulse flex items-center justify-center"
    >
      <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
      </svg>
    </div>

    <!-- Modern image with WebP support -->
    <picture v-show="!loading && !error">
      <source 
        v-if="webpSrc" 
        :srcset="webpSrc" 
        type="image/webp"
      />
      <source 
        v-if="avifSrc" 
        :srcset="avifSrc" 
        type="image/avif"
      />
      <img
        ref="image"
        :src="currentSrc"
        :alt="alt"
        :class="imageClass"
        @load="onLoad"
        @error="onError"
        loading="lazy"
        decoding="async"
        :fetchpriority="priority"
      />
    </picture>

    <!-- Error placeholder -->
    <div 
      v-if="error" 
      class="absolute inset-0 bg-gray-100 flex items-center justify-center"
    >
      <div class="text-center">
        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <span class="text-sm text-gray-500">Image not available</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  src: {
    type: String,
    required: true
  },
  alt: {
    type: String,
    default: ''
  },
  containerClass: {
    type: String,
    default: ''
  },
  imageClass: {
    type: String,
    default: 'w-full h-full object-cover'
  },
  fallbackSrc: {
    type: String,
    default: '/placeholder-course.jpg'
  },
  // Modern image format support
  webpSrc: {
    type: String,
    default: ''
  },
  avifSrc: {
    type: String,
    default: ''
  },
  // Image priority for performance
  priority: {
    type: String,
    default: 'auto',
    validator: (value) => ['high', 'low', 'auto'].includes(value)
  },
  // Responsive image sources
  srcset: {
    type: String,
    default: ''
  },
  sizes: {
    type: String,
    default: ''
  }
})

const imageContainer = ref(null)
const image = ref(null)
const loading = ref(true)
const error = ref(false)
const observer = ref(null)

// Auto-generate WebP source if not provided
const webpSrc = computed(() => {
  if (props.webpSrc) return props.webpSrc
  if (props.src && (props.src.endsWith('.jpg') || props.src.endsWith('.jpeg') || props.src.endsWith('.png'))) {
    return props.src.replace(/\.(jpg|jpeg|png)$/i, '.webp')
  }
  return null
})

// Auto-generate AVIF source if not provided  
const avifSrc = computed(() => {
  if (props.avifSrc) return props.avifSrc
  if (props.src && (props.src.endsWith('.jpg') || props.src.endsWith('.jpeg') || props.src.endsWith('.png'))) {
    return props.src.replace(/\.(jpg|jpeg|png)$/i, '.avif')
  }
  return null
})

// Current source with fallback logic
const currentSrc = computed(() => {
  return error.value ? props.fallbackSrc : props.src
})

const onLoad = () => {
  loading.value = false
  error.value = false
}

const onError = () => {
  loading.value = false
  if (currentSrc.value !== props.fallbackSrc) {
    error.value = true
  }
}

// Intersection Observer for lazy loading
const setupIntersectionObserver = () => {
  if ('IntersectionObserver' in window) {
    observer.value = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          // Start loading the image when it comes into view
          if (image.value && !image.value.src) {
            image.value.src = currentSrc.value
          }
          observer.value.unobserve(entry.target)
        }
      })
    }, {
      threshold: 0.1,
      rootMargin: '50px'
    })

    if (imageContainer.value) {
      observer.value.observe(imageContainer.value)
    }
  } else {
    // Fallback for browsers without IntersectionObserver
    loading.value = false
  }
}

onMounted(() => {
  if (image.value && image.value.complete) {
    onLoad()
  }
  setupIntersectionObserver()
})

onUnmounted(() => {
  if (observer.value) {
    observer.value.disconnect()
  }
})
</script>

<style scoped>
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
}
</style>
