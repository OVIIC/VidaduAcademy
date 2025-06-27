<template>
  <div id="app" class="min-h-screen bg-white">
    <AppNavigation />
    
    <main class="flex-1">
      <router-view v-slot="{ Component, route }">
        <transition name="fade" mode="out-in">
          <component :is="Component" :key="route.path" />
        </transition>
      </router-view>
    </main>
    
    <AppFooter />
    
    <!-- Performance Dashboard (development only) -->
    <PerformanceDashboard v-if="isDevelopment" />
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import AppNavigation from '@/components/layout/AppNavigation.vue'
import AppFooter from '@/components/layout/AppFooter.vue'
import PerformanceDashboard from '@/components/debug/PerformanceDashboard.vue'

const authStore = useAuthStore()

// Show performance dashboard only in development
const isDevelopment = computed(() => {
  return import.meta.env.DEV || import.meta.env.VITE_SHOW_PERFORMANCE === 'true'
})

onMounted(() => {
  // Initialize auth state from localStorage
  authStore.initializeAuth()
})
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
