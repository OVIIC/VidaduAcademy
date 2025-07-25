<template>
  <div id="app" class="min-h-screen flex flex-col">
    <!-- Skip to main content link for accessibility -->
    <a 
      href="#main-content" 
      class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-primary-600 text-white px-4 py-2 rounded-md z-50"
    >
      Preskočiť na hlavný obsah
    </a>
    
    <AppNavigationMobile />
    
    <main id="main-content" class="flex-1 pt-16 pb-safe-bottom">
      <!-- pt-16 compenzuje fixed navigation výšku -->
      <router-view v-slot="{ Component, route }">
        <transition 
          name="page-transition" 
          mode="out-in"
          @enter="onPageEnter"
          @leave="onPageLeave"
        >
          <component :is="Component" :key="route.path" />
        </transition>
      </router-view>
    </main>
    
    <AppFooter />
    
    <!-- Performance Dashboard (development only) -->
    <PerformanceDashboard v-if="isDevelopment" />
    
    <!-- Global Loading Overlay -->
    <div 
      v-if="isGlobalLoading" 
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg p-6 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Načítava...</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, computed, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'
import AppNavigationMobile from '@/components/layout/AppNavigationMobile.vue'
import AppFooter from '@/components/layout/AppFooter.vue'
import PerformanceDashboard from '@/components/debug/PerformanceDashboard.vue'

const route = useRoute()
const authStore = useAuthStore()
const themeStore = useThemeStore()

// Global loading state
const isGlobalLoading = ref(false)

// Show performance dashboard only in development
const isDevelopment = computed(() => {
  return import.meta.env.DEV || import.meta.env.VITE_SHOW_PERFORMANCE === 'true'
})

// Page transition handlers
const onPageEnter = (el) => {
  // Trigger any enter animations
}

const onPageLeave = (el) => {
  // Cleanup any leaving page resources
}

// Watch for route changes to handle loading states
watch(route, (to, from) => {
  // Optional: Show loading for certain route transitions
  if (to.meta?.requiresLoading) {
    isGlobalLoading.value = true
    setTimeout(() => {
      isGlobalLoading.value = false
    }, 500)
  }
})

onMounted(() => {
  // Initialize auth state from localStorage
  authStore.initializeAuth()
  
  // Initialize theme system
  themeStore.initializeTheme()
  
  // Add viewport meta tag for mobile optimization
  if (!document.querySelector('meta[name="viewport"]')) {
    const viewport = document.createElement('meta')
    viewport.name = 'viewport'
    viewport.content = 'width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes'
    document.head.appendChild(viewport)
  }
  
  // Add theme color for mobile browsers (will be updated by theme store)
  if (!document.querySelector('meta[name="theme-color"]')) {
    const themeColor = document.createElement('meta')
    themeColor.name = 'theme-color'
    themeColor.content = '#6366f1' // Initial color, updated by theme
    document.head.appendChild(themeColor)
  }
})
</script>

<style>
/* Global responsive styles */
html {
  scroll-behavior: smooth;
}

body {
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Page transitions */
.page-transition-enter-active,
.page-transition-leave-active {
  transition: all 0.3s ease;
}

.page-transition-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.page-transition-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Safe area insets for mobile devices */
@supports (padding: max(0px)) {
  .pt-safe-top {
    padding-top: max(env(safe-area-inset-top), 0px);
  }
  
  .pb-safe-bottom {
    padding-bottom: max(env(safe-area-inset-bottom), 0px);
  }
}

/* Focus styles for accessibility */
*:focus {
  outline: 2px solid #6366f1;
  outline-offset: 2px;
}

/* Touch improvements */
@media (hover: none) {
  .hover\:scale-105:hover {
    transform: none;
  }
  
  .hover\:shadow-lg:hover {
    box-shadow: inherit;
  }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
</style>
