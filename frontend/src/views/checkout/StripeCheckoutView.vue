<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
      <!-- Card container -->
      <div class="bg-white rounded-lg shadow-lg p-8 text-center">
        <!-- Stripe logo placeholder -->
        <div class="mb-6">
          <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
          </div>
          <h1 class="text-2xl font-bold text-gray-900 mb-2">Stripe Checkout</h1>
          <p class="text-gray-600">Platobný terminál</p>
        </div>

        <!-- Course info -->
        <div v-if="courseInfo" class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
          <h3 class="font-semibold text-gray-900 mb-2">Objednávka:</h3>
          <div class="flex justify-between items-center mb-2">
            <span class="text-gray-600">Kurz:</span>
            <span class="font-medium">{{ courseInfo.title }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-600">Cena:</span>
            <span class="font-bold text-primary-600">${{ courseInfo.price }}</span>
          </div>
        </div>

        <!-- Placeholder message -->
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 mb-6">
          <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
          </svg>
          <h2 class="text-xl font-semibold text-gray-900 mb-2">Tu bude Stripe Terminal</h2>
          <p class="text-gray-600 mb-4">
            Platobný formulár a Stripe integrácia budú implementované neskôr.
          </p>
          <div class="text-sm text-gray-500">
            Session ID: <code class="bg-gray-100 px-2 py-1 rounded">{{ sessionId }}</code>
          </div>
        </div>

        <!-- Action buttons -->
        <div class="space-y-3">
          <button
            @click="simulateSuccess"
            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200"
          >
            🎉 Simulovať úspešnú platbu
          </button>
          
          <button
            @click="simulateCancel"
            class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200"
          >
            ❌ Simulovať zrušenie platby
          </button>
          
          <button
            @click="goBack"
            class="w-full border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-3 px-4 rounded-lg transition duration-200"
          >
            ← Späť na kurzy
          </button>
        </div>

        <!-- Development info -->
        <div class="mt-6 pt-4 border-t border-gray-200 text-xs text-gray-500">
          <p>🚧 Vývojová stránka - Stripe integrácia bude pridaná neskôr</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

// Generate a mock session ID
const sessionId = ref(`cs_dev_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`)

// Course information from query parameters
const courseInfo = ref(null)

onMounted(() => {
  // Get course info from query parameters
  if (route.query.courseTitle || route.query.coursePrice) {
    courseInfo.value = {
      title: route.query.courseTitle || 'Neznámy kurz',
      price: route.query.coursePrice || '0'
    }
  }
})

const simulateSuccess = () => {
  // Simulate successful payment
  alert('🎉 Úspešná platba! Kurz bol pridaný do vašich kurzov.')
  router.push('/my-courses')
}

const simulateCancel = () => {
  // Simulate cancelled payment
  alert('❌ Platba bola zrušená.')
  goBack()
}

const goBack = () => {
  // Go back to courses or previous page
  if (window.history.length > 1) {
    router.back()
  } else {
    router.push('/courses')
  }
}
</script>

<style scoped>
code {
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
}
</style>
