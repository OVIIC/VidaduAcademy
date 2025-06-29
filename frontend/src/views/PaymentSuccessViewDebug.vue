<template>
  <div class="min-h-screen bg-gradient-to-br from-green-50 to-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Success Icon -->
      <div class="text-center">
        <div class="mx-auto h-20 w-20 bg-green-100 rounded-full flex items-center justify-center">
          <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
        </div>
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Platba úspešná!</h2>
        <p class="mt-2 text-sm text-gray-600">
          Ďakujeme za váš nákup. Kurz bol pridaný do vašich kurzov.
        </p>
      </div>

      <!-- Debug Information -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Debug Info</h3>
        <div class="space-y-2 text-sm">
          <p><strong>Session ID:</strong> {{ sessionId || 'Nie je k dispozícii' }}</p>
          <p><strong>URL:</strong> {{ fullUrl }}</p>
          <p><strong>Route params:</strong> {{ JSON.stringify(route.query) }}</p>
          <p><strong>Čas:</strong> {{ new Date().toLocaleString() }}</p>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="space-y-3">
        <router-link
          to="/my-courses"
          class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200"
        >
          Zobraziť moje kurzy
        </router-link>
        
        <router-link
          to="/dashboard"
          class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200"
        >
          Ísť na dashboard
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const sessionId = computed(() => route.query.session_id)
const fullUrl = computed(() => window.location.href)

// Log for debugging
console.log('PaymentSuccessViewDebug loaded')
console.log('Route query:', route.query)
console.log('Session ID:', sessionId.value)
</script>
