<template>
  <div id="app" class="min-h-screen bg-white">
    <AppNavigation />
    
    <main class="flex-1">
      <router-view />
    </main>
    
    <AppFooter />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import AppNavigation from '@/components/layout/AppNavigation.vue'
import AppFooter from '@/components/layout/AppFooter.vue'

const authStore = useAuthStore()

onMounted(() => {
  // Initialize auth state from localStorage
  authStore.initializeAuth()
})
</script>
