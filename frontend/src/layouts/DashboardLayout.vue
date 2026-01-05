<template>
  <div class="h-screen flex bg-dark-950 overflow-hidden relative">
    <!-- Background Effects (Persistent) -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-[500px] -left-1/4 w-[1000px] h-[1000px] bg-primary-600/20 rounded-full blur-[120px] opacity-30 animate-pulse-slow"></div>
        <!-- Middle Blob -->
        <div class="absolute top-[40%] -right-1/4 w-[800px] h-[800px] bg-secondary-600/10 rounded-full blur-[100px] opacity-30 animate-pulse-slow delay-500"></div>
        <!-- Bottom Blob -->
        <div class="absolute -bottom-[200px] -left-1/4 w-[800px] h-[800px] bg-primary-600/20 rounded-full blur-[120px] opacity-30 animate-pulse-slow delay-1000"></div>
        <div class="absolute top-0 left-0 w-full h-full bg-[url('/images/grid-pattern.svg')] bg-repeat opacity-[0.03]"></div>
    </div>
    <AppSidebar class="hidden md:flex" />
    
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Mobile header -->
      <div class="md:hidden bg-dark-900 border-b border-dark-700 p-4 flex items-center justify-between">
        <router-link to="/" class="font-bold text-xl text-white">Vidadu</router-link>
        <button @click="isSidebarOpen = !isSidebarOpen" class="text-gray-400 hover:text-white">
          <span class="sr-only">Open sidebar</span>
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>

      <!-- Mobile sidebar overlay -->
      <div v-if="isSidebarOpen" class="fixed inset-0 z-40 md:hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75" aria-hidden="true" @click="isSidebarOpen = false"></div>
        <div class="relative flex-1 flex flex-col max-w-xs w-full bg-dark-900 h-full">
          <div class="absolute top-0 right-0 -mr-12 pt-2">
            <button @click="isSidebarOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
              <span class="sr-only">Close sidebar</span>
              <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
            <AppSidebar />
          </div>
        </div>
      </div>

      <main class="relative flex-1 overflow-y-auto focus:outline-none">
        <slot></slot>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import AppSidebar from '@/components/layout/AppSidebar.vue'

const isSidebarOpen = ref(false)
</script>
