<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div 
      class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
      @click="closeModal"
    ></div>
    
    <!-- Modal -->
    <div class="flex min-h-screen items-center justify-center p-4">
      <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">
        <!-- Close button -->
        <button
          @click="closeModal"
          class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition duration-200"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <!-- Modal content -->
        <div class="p-6">
          <!-- Icon -->
          <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-primary-100 rounded-full">
            <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>

          <!-- Title -->
          <h3 class="text-xl font-semibold text-gray-900 text-center mb-2">
            Prihlásenie vyžadované
          </h3>

          <!-- Message -->
          <p class="text-gray-600 text-center mb-6">
            Pre zakúpenie kurzu sa musíte najprv zaregistrovať alebo prihlásiť do svojho účtu.
          </p>

          <!-- Course info -->
          <div v-if="courseTitle" class="bg-gray-50 rounded-lg p-4 mb-6">
            <h4 class="font-medium text-gray-900 mb-1">Kurz:</h4>
            <p class="text-sm text-gray-600">{{ courseTitle }}</p>
            <p v-if="coursePrice" class="text-lg font-bold text-primary-600 mt-2">
              ${{ coursePrice }}
            </p>
          </div>

          <!-- Action buttons -->
          <div class="space-y-3">
            <button
              @click="goToLogin"
              class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200"
            >
              Prihlásiť sa
            </button>
            
            <button
              @click="goToRegister"
              class="w-full border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-3 px-4 rounded-lg transition duration-200"
            >
              Zaregistrovať sa
            </button>
          </div>

          <!-- Additional info -->
          <div class="mt-6 pt-4 border-t border-gray-200">
            <div class="flex items-center text-sm text-gray-500">
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
              </svg>
              Vaše údaje sú v bezpečí a chránené
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  courseTitle: {
    type: String,
    default: ''
  },
  coursePrice: {
    type: [String, Number],
    default: null
  }
})

const emit = defineEmits(['close'])

const router = useRouter()

const closeModal = () => {
  emit('close')
}

const goToLogin = () => {
  closeModal()
  router.push('/login')
}

const goToRegister = () => {
  closeModal()
  router.push('/register')
}
</script>

<style scoped>
/* Add any custom styles if needed */
</style>
