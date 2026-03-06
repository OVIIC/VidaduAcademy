<template>
  <div class="min-h-screen text-white flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-dark-950 relative overflow-hidden">
    <!-- Background Effects -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-1/2 -left-1/4 w-[1000px] h-[1000px] bg-primary-600/20 rounded-full blur-[120px] opacity-30 animate-pulse-slow"></div>
      <div class="absolute -bottom-1/2 -right-1/4 w-[800px] h-[800px] bg-secondary-600/20 rounded-full blur-[100px] opacity-30 animate-pulse-slow delay-1000"></div>
    </div>
    <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
      <div class="flex justify-center">
        <router-link to="/" class="w-16 h-16 bg-dark-900/50 backdrop-blur-xl border border-dark-700/50 rounded-2xl flex items-center justify-center shadow-lg transition-transform hover:scale-105">
          <img src="/images/video-academy-icon.png" alt="Vidadu Academy" class="w-10 h-10 object-contain" />
        </router-link>
      </div>
      <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-white mb-2">
        Obnova hesla
      </h2>
      <p class="text-center text-sm text-dark-300">
        Zadajte svoj e-mail a my vám pošleme odkaz na obnovenie hesla.
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md relative z-10">
      <div class="bg-dark-900/50 backdrop-blur-xl border border-dark-700/50 rounded-2xl py-8 px-4 shadow-xl sm:px-10">
        
        <div v-if="successMessage" class="rounded-xl bg-green-500/10 border border-green-500/30 p-4 mb-6">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-green-300">{{ successMessage }}</p>
            </div>
          </div>
        </div>

        <form v-else class="space-y-6" @submit.prevent="handleSubmit">
          <div>
            <label for="email" class="block text-sm font-medium text-dark-300 mb-1.5">
              Emailová adresa
            </label>
            <div class="relative">
              <input
                id="email"
                v-model="email"
                name="email"
                type="email"
                required
                :disabled="loading"
                class="block w-full rounded-xl border-dark-700 bg-dark-800/50 text-white placeholder-dark-500 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm backdrop-blur-sm transition-colors py-2.5"
                placeholder="janko@example.com"
              />
            </div>
            <p v-if="error" class="mt-2 text-sm text-red-400">{{ error }}</p>
          </div>

          <div>
            <button
              type="submit"
              :disabled="loading"
              class="group relative w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-primary-600 hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-all duration-300"
            >
              <span v-if="loading" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Odosielanie...
              </span>
              <span v-else>Odoslať odkaz na obnovenie</span>
            </button>
          </div>
        </form>

        <div class="mt-6 text-center">
          <router-link to="/login" class="text-sm font-medium text-primary-400 hover:text-primary-300 transition-colors">
            &larr; Späť na prihlásenie
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { authService } from '@/services'
import { useHead } from '@unhead/vue'

useHead({
  title: 'Zabudnuté heslo | VidaduAcademy',
})

const email = ref('')
const loading = ref(false)
const error = ref('')
const successMessage = ref('')

const handleSubmit = async () => {
  error.value = ''
  successMessage.value = ''
  loading.value = true

  try {
    const response = await authService.forgotPassword(email.value)
    successMessage.value = response.message || 'Odkaz na obnovenie hesla bol odoslaný.'
  } catch (err) {
    error.value = err.response?.data?.message || 'Nastala chyba pri odosielaní.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@keyframes pulse-slow {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 0.2; transform: scale(1.1); }
}

.animate-pulse-slow {
  animation: pulse-slow 8s infinite ease-in-out;
}

.delay-1000 { animation-delay: 1s; }
</style>
