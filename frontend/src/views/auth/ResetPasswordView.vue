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
        Nové heslo
      </h2>
      <p class="text-center text-sm text-dark-300">
        Zadajte vaše nové heslo
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
          <div class="mt-4 text-center">
            <router-link to="/login" class="text-sm font-medium text-primary-400 hover:text-primary-300 transition-colors">
              Prejsť na prihlásenie
            </router-link>
          </div>
        </div>

        <form v-else class="space-y-6" @submit.prevent="handleSubmit">
          <div>
            <label for="password" class="block text-sm font-medium text-dark-300 mb-1.5">
              Nové heslo
            </label>
            <div class="relative">
              <input
                id="password"
                v-model="form.password"
                name="password"
                type="password"
                required
                :disabled="loading"
                class="block w-full rounded-xl border-dark-700 bg-dark-800/50 text-white placeholder-dark-500 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm backdrop-blur-sm transition-colors py-2.5"
                placeholder="••••••••"
              />
            </div>
            <p v-if="errors.password" class="mt-2 text-sm text-red-400">{{ errors.password[0] }}</p>
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-dark-300 mb-1.5">
              Potvrdiť nové heslo
            </label>
            <div class="relative">
              <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                name="password_confirmation"
                type="password"
                required
                :disabled="loading"
                class="block w-full rounded-xl border-dark-700 bg-dark-800/50 text-white placeholder-dark-500 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm backdrop-blur-sm transition-colors py-2.5"
                placeholder="••••••••"
              />
            </div>
          </div>

          <p v-if="globalError" class="mt-2 text-sm text-red-400">{{ globalError }}</p>

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
                Obnovovanie...
              </span>
              <span v-else>Obnoviť heslo</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { authService } from '@/services'
import { useHead } from '@unhead/vue'
import { useRoute, useRouter } from 'vue-router'

useHead({
  title: 'Nové heslo | VidaduAcademy',
})

const route = useRoute()
const router = useRouter()

const form = ref({
  email: '',
  password: '',
  password_confirmation: '',
  token: ''
})

const loading = ref(false)
const errors = ref({})
const globalError = ref('')
const successMessage = ref('')

onMounted(() => {
  form.value.token = route.query.token || ''
  form.value.email = route.query.email || ''
  
  if (!form.value.token || !form.value.email) {
    globalError.value = 'Chybný alebo chýbajúci odkaz na obnovenie hesla.'
  }
})

const handleSubmit = async () => {
  errors.value = {}
  globalError.value = ''
  successMessage.value = ''
  loading.value = true

  try {
    const response = await authService.resetPassword(form.value)
    successMessage.value = response.message || 'Vaše heslo bolo úspešne obnovené.'
  } catch (err) {
    if (err.response?.status === 422 || err.response?.status === 400) {
      if (err.response.data.errors) {
         errors.value = err.response.data.errors
      } else {
         globalError.value = err.response.data.message
      }
    } else {
      globalError.value = 'Nastala chyba pri obnove hesla.'
    }
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
