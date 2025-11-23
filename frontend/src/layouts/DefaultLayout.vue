<template>
  <div class="min-h-screen flex flex-col">
    <AppNavigation />
    
    <main id="main-content" class="flex-1 pb-safe-bottom">
      <slot></slot>
    </main>
    
    <AppFooter />
  </div>
</template>

<script setup>
import AppNavigation from '@/components/layout/AppNavigation.vue'
import AppFooter from '@/components/layout/AppFooter.vue'
</script>
