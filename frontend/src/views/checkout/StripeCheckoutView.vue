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
            @click="proceedToCheckout"
            :disabled="isProcessing"
            class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium py-3 px-4 rounded-lg transition duration-200"
          >
            <span v-if="isProcessing" class="flex items-center justify-center">
              <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
              Pripravujem platbu...
            </span>
            <span v-else>💳 Prejsť na platbu (Stripe)</span>
          </button>
          
          <button
            @click="goBack"
            class="w-full border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-3 px-4 rounded-lg transition duration-200"
          >
            ← Späť na kurzy
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useEnrollmentStore } from '@/stores/enrollment'
import { paymentService } from '@/services'
import { useToast } from 'vue-toastification'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const enrollmentStore = useEnrollmentStore()
const toast = useToast()

// Generate a mock session ID
const sessionId = ref(`cs_dev_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`)

// Course information from query parameters
const courseInfo = ref(null)
const isProcessing = ref(false)

onMounted(() => {
  // Get course info from query parameters
  if (route.query.courseTitle || route.query.coursePrice) {
    courseInfo.value = {
      id: route.query.courseId || null,
      title: route.query.courseTitle || 'Neznámy kurz',
      price: route.query.coursePrice || '0'
    }
  }
})

const proceedToCheckout = async () => {
  if (!courseInfo.value?.id) {
    toast.error('Chyba: ID kurzu nenájdené')
    return
  }

  if (!authStore.user) {
    toast.error('Chyba: Užívateľ nie je prihlásený')
    return
  }

  isProcessing.value = true
  
  try {
    const successUrl = `${window.location.origin}/payment/success`
    const cancelUrl = `${window.location.origin}/course/${route.query.courseSlug || courseInfo.value.id}`
    
    // Call the create checkout session API
    const response = await paymentService.createCheckoutSession(
      parseInt(courseInfo.value.id),
      successUrl,
      cancelUrl
    )
    
    // Redirect to Stripe Hosted Checkout Page
    if (response && response.checkout_url) {
      window.location.href = response.checkout_url
    } else {
      throw new Error('Nepodarilo sa získať Stripe Checkout adresu')
    }
    
  } catch (error) {
    console.error('Checkout failed:', error)
    
    if (error.response?.status === 422) {
      toast.error('Tento kurz už vlastníte')
      router.push('/my-courses')
    } else {
      toast.error('Nepodarilo sa prejsť na platbu. Skúste to prosím neskôr.')
    }
  } finally {
    isProcessing.value = false
  }
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
