<template>
  <div class="min-h-screen text-white flex flex-col justify-center py-12 pt-24 sm:px-6 lg:px-8 bg-dark-950 relative overflow-hidden">
    <!-- Background Effects -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-1/2 -left-1/4 w-[1000px] h-[1000px] bg-primary-600/20 rounded-full blur-[120px] opacity-30 animate-pulse-slow"></div>
      <div class="absolute -bottom-1/2 -right-1/4 w-[800px] h-[800px] bg-secondary-600/20 rounded-full blur-[100px] opacity-30 animate-pulse-slow delay-1000"></div>
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-[url('/images/grid-pattern.svg')] bg-repeat opacity-[0.03] blur-[3px]"></div>
    </div>
    <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
      <div class="flex justify-center">
        <router-link to="/" class="w-20 h-20 bg-dark-900/50 backdrop-blur-xl border border-dark-700/50 rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/10 transform hover:scale-105 transition-transform duration-300">
          <img src="/images/video-academy-icon.png" alt="Vidadu Academy" class="w-12 h-12 object-contain" />
        </router-link>
      </div>
      <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-white">
        Lektorský Priemysel
      </h2>
      <p class="mt-2 text-center text-sm text-dark-300 font-medium">
        Prihlásenie pre lektorov
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md relative z-10">
      <div class="bg-dark-900/50 backdrop-blur-xl border border-dark-700/50 rounded-2xl py-8 px-4 shadow-xl sm:px-10 hover:border-dark-600 transition-colors duration-300">
        <form class="space-y-6" @submit.prevent="handleSubmit">
          
          <div v-if="securityNotice" class="bg-gradient-to-br from-yellow-500/10 via-yellow-600/10 to-yellow-500/10 backdrop-blur-xl border border-yellow-500/30 rounded-xl p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-yellow-200">{{ securityNotice }}</p>
              </div>
            </div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-dark-300 mb-1.5">
              Emailová adresa
            </label>
            <div class="relative">
              <input
                id="email"
                v-model="form.email"
                name="email"
                type="email"
                autocomplete="email"
                required
                :disabled="loading"
                class="block w-full rounded-xl border-dark-700 bg-dark-800/50 text-white placeholder-dark-500 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm backdrop-blur-sm transition-colors py-2.5"
                placeholder="lektor@example.com"
                @input="clearSecurityNotice"
              />
            </div>
            <div v-if="errors.email" class="mt-1 text-sm text-red-400">
              {{ errors.email[0] }}
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-dark-300 mb-1.5">
              Heslo
            </label>
            <div class="relative">
              <input
                id="password"
                v-model="form.password"
                name="password"
                type="password"
                autocomplete="current-password"
                required
                :disabled="loading"
                class="block w-full rounded-xl border-dark-700 bg-dark-800/50 text-white placeholder-dark-500 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm backdrop-blur-sm transition-colors py-2.5"
                placeholder="••••••••"
                @input="clearSecurityNotice"
              />
            </div>
            <div v-if="errors.password" class="mt-1 text-sm text-red-400">
              {{ errors.password[0] }}
            </div>
          </div>

          <div v-if="rateLimitNotice" class="bg-gradient-to-br from-red-500/10 via-red-600/10 to-red-500/10 backdrop-blur-xl border border-red-500/30 rounded-xl p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-red-200">{{ rateLimitNotice }}</p>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input
                id="remember-me"
                v-model="form.remember"
                name="remember-me"
                type="checkbox"
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-dark-600 rounded bg-dark-800/50"
              />
              <label for="remember-me" class="ml-2 block text-sm text-dark-300">
                Zapamätať si ma
              </label>
            </div>

            <div class="text-sm">
              <router-link to="/forgot-password" class="font-medium text-primary-400 hover:text-primary-300 transition-colors">
                Zabudli ste heslo?
              </router-link>
            </div>
          </div>

          <div>
            <button
              type="submit"
              :disabled="loading"
              class="group relative w-full flex justify-center items-center py-4 px-4 border border-transparent rounded-2xl shadow-lg text-sm font-bold text-white bg-primary-600 hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-all duration-300 transform hover:-translate-y-1 overflow-hidden hover:shadow-primary-500/25"
            >
              <span class="relative z-10 flex items-center">
                <span v-if="loading" class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Prihlasovanie...
                </span>
                <span v-else>Prihlásiť sa (Lektor)</span>
              </span>
              <div class="absolute inset-0 bg-gradient-to-r from-primary-500 via-white/20 to-primary-500 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700 ease-in-out"></div>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useAuthLogic } from '@/composables/useAuthLogic'
import { useHead } from '@unhead/vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'

useHead({
  title: 'Prihlásenie Lektora | VidaduAcademy',
  meta: [
    {
      name: 'description',
      content: 'Prihláste sa do lektorského prostredia na VidaduAcademy.'
    }
  ]
})

const authStore = useAuthStore()
const router = useRouter()
const toast = useToast()

const { 
  form, 
  loading, 
  errors, 
  securityNotice, 
  rateLimitNotice, 
  clearSecurityNotice, 
  handleLogin 
} = useAuthLogic()

const handleSubmit = async () => {
  await handleLogin()
  
  // Verify if it's an instructor
  if (authStore.user) {
    if (!authStore.user.is_instructor && !authStore.hasRole('admin') && !authStore.hasRole('instructor')) {
      toast.error('Na prístup do lektorského portálu nemáte oprávnenie.')
      await authStore.logout()
      router.push('/instructor/login')
    } else {
      router.push('/instructor/dashboard')
    }
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

.animate-gradient-x {
  background-size: 200% 200%;
  animation: gradientX 4s ease infinite;
}

@keyframes gradientX {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
</style>
