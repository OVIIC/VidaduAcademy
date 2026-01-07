<template>
  <div class="min-h-screen text-white flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-dark-950 relative overflow-hidden">
    <!-- Background Effects -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-1/2 -left-1/4 w-[1000px] h-[1000px] bg-primary-600/20 rounded-full blur-[120px] opacity-30 animate-pulse-slow"></div>
      <div class="absolute -bottom-1/2 -right-1/4 w-[800px] h-[800px] bg-secondary-600/20 rounded-full blur-[100px] opacity-30 animate-pulse-slow delay-1000"></div>
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-[url('/images/grid-pattern.svg')] bg-repeat opacity-[0.03]"></div>
    </div>
    <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
      <div class="flex justify-center">
        <router-link to="/" class="w-14 h-14 bg-gradient-to-br from-primary-500 to-secondary-500 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/20 transform hover:scale-105 transition-transform duration-300">
          <span class="text-white font-bold text-2xl">V</span>
        </router-link>
      </div>
      <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-white">
        <span class="block text-white mb-1">Vytvorte si účet</span>
        <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-400 via-primary-500 to-secondary-500 animate-gradient-x text-sm uppercase tracking-wider font-extrabold">
          a začnite rásť
        </span>
      </h2>
      <p class="mt-2 text-center text-sm text-dark-300">
        Alebo
        <router-link to="/login" class="font-medium text-primary-400 hover:text-primary-300 transition-colors">
          sa prihláste do existujúceho účtu
        </router-link>
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md relative z-10">
      <div class="bg-dark-900/50 backdrop-blur-xl border border-dark-700/50 rounded-2xl py-8 px-4 shadow-xl sm:px-10 hover:border-dark-600 transition-colors duration-300">
        <!-- Social Login -->
        <div class="mb-6">
          <a
            :href="`${apiUrl}/auth/google/redirect`"
            class="w-full flex justify-center items-center px-4 py-3 border border-dark-700 rounded-xl shadow-sm text-sm font-medium text-white bg-dark-800/50 hover:bg-dark-700/80 transition-all duration-200 hover:border-dark-600 group"
          >
            <svg class="h-5 w-5 mr-3 transition-transform group-hover:scale-110" viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg">
              <g transform="matrix(1, 0, 0, 1, 27.009001, -39.238998)">
                <path fill="#4285F4" d="M -3.264 51.509 C -3.264 50.719 -3.334 49.969 -3.454 49.239 L -14.754 49.239 L -14.754 53.749 L -8.284 53.749 C -8.574 55.229 -9.424 56.479 -10.684 57.329 L -10.684 60.329 L -6.824 60.329 C -4.564 58.239 -3.264 55.159 -3.264 51.509 Z"/>
                <path fill="#34A853" d="M -14.754 63.239 C -11.514 63.239 -8.804 62.159 -6.824 60.329 L -10.684 57.329 C -11.764 58.049 -13.134 58.489 -14.754 58.489 C -17.884 58.489 -20.534 56.379 -21.484 53.529 L -25.464 53.529 L -25.464 56.619 C -23.494 60.539 -19.444 63.239 -14.754 63.239 Z"/>
                <path fill="#FBBC05" d="M -21.484 53.529 C -21.734 52.809 -21.864 52.039 -21.864 51.239 C -21.864 50.439 -21.724 49.669 -21.484 48.949 L -21.484 45.859 L -25.464 45.859 C -26.284 47.479 -26.754 49.299 -26.754 51.239 C -26.754 53.179 -26.284 54.999 -25.464 56.619 L -21.484 53.529 Z"/>
                <path fill="#EA4335" d="M -14.754 43.989 C -12.984 43.989 -11.404 44.599 -10.154 45.789 L -6.734 42.369 C -8.804 40.429 -11.514 39.239 -14.754 39.239 C -19.444 39.239 -23.494 41.939 -25.464 45.859 L -21.484 48.949 C -20.534 46.099 -17.884 43.989 -14.754 43.989 Z"/>
              </g>
            </svg>
            <span class="text-dark-200 group-hover:text-white transition-colors">Registrovať sa cez Google</span>
          </a>
        </div>

        <div class="relative mb-6">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-dark-700"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-dark-900/50 text-dark-400 backdrop-blur-xl">Alebo cez email</span>
          </div>
        </div>

        <form class="space-y-5" @submit.prevent="handleSubmit">
          <div>
            <label for="name" class="block text-sm font-medium text-dark-300 mb-1.5">
              Celé meno
            </label>
            <div class="relative">
              <input
                id="name"
                v-model="form.name"
                name="name"
                type="text"
                autocomplete="name"
                required
                class="block w-full rounded-xl border-dark-700 bg-dark-800/50 text-white placeholder-dark-500 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm backdrop-blur-sm transition-colors py-2.5"
                placeholder="Janko Hraško"
              />
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
                class="block w-full rounded-xl border-dark-700 bg-dark-800/50 text-white placeholder-dark-500 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm backdrop-blur-sm transition-colors py-2.5"
                placeholder="janko@example.com"
              />
            </div>
          </div>

          <div>
            <label for="youtube_channel" class="block text-sm font-medium text-dark-300 mb-1.5">
              URL YouTube kanála <span class="text-dark-500 font-normal">(voliteľné)</span>
            </label>
            <div class="relative">
              <input
                id="youtube_channel"
                v-model="form.youtube_channel"
                name="youtube_channel"
                type="url"
                placeholder="https://youtube.com/@vaškanál"
                class="block w-full rounded-xl border-dark-700 bg-dark-800/50 text-white placeholder-dark-500 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm backdrop-blur-sm transition-colors py-2.5"
              />
            </div>
          </div>

          <div>
            <label for="subscribers_count" class="block text-sm font-medium text-dark-300 mb-1.5">
              Súčasný počet odberateľov <span class="text-dark-500 font-normal">(voliteľné)</span>
            </label>
            <div class="relative">
              <input
                id="subscribers_count"
                v-model="form.subscribers_count"
                name="subscribers_count"
                type="number"
                min="0"
                placeholder="0"
                class="block w-full rounded-xl border-dark-700 bg-dark-800/50 text-white placeholder-dark-500 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm backdrop-blur-sm transition-colors py-2.5"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                  autocomplete="new-password"
                  required
                  class="block w-full rounded-xl border-dark-700 bg-dark-800/50 text-white placeholder-dark-500 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm backdrop-blur-sm transition-colors py-2.5"
                  placeholder="••••••••"
                />
              </div>
            </div>

            <div>
              <label for="password_confirmation" class="block text-sm font-medium text-dark-300 mb-1.5">
                Potvrdiť heslo
              </label>
              <div class="relative">
                <input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  name="password_confirmation"
                  type="password"
                  autocomplete="new-password"
                  required
                  class="block w-full rounded-xl border-dark-700 bg-dark-800/50 text-white placeholder-dark-500 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm backdrop-blur-sm transition-colors py-2.5"
                  placeholder="••••••••"
                />
              </div>
            </div>
          </div>

          <div class="flex items-start pt-2">
            <div class="flex h-5 items-center">
              <input
                id="agree-terms"
                v-model="form.agreeTerms"
                name="agree-terms"
                type="checkbox"
                required
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-dark-600 rounded bg-dark-800/50"
              />
            </div>
            <div class="ml-3 text-sm">
              <label for="agree-terms" class="font-medium text-dark-300">
                Súhlasím s
                <a href="#" class="text-primary-400 hover:text-primary-300 transition-colors">Podmienkami služby</a>
                a
                <a href="#" class="text-primary-400 hover:text-primary-300 transition-colors">Zásadami ochrany súkromia</a>
              </label>
            </div>
          </div>

          <div class="pt-4">
            <button
              type="submit"
              :disabled="authStore.loading"
              class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-primary-600 to-secondary-600 hover:from-primary-500 hover:to-secondary-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-all duration-300 transform hover:-translate-y-0.5"
            >
              <span v-if="authStore.loading" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Vytváram účet...
              </span>
              <span v-else>Vytvoriť účet zadarmo</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'

const apiUrl = import.meta.env.VITE_API_URL

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const form = reactive({
  name: '',
  email: '',
  youtube_channel: '',
  subscribers_count: 0,
  password: '',
  password_confirmation: '',
  agreeTerms: false,
})

const handleSubmit = async () => {
  if (form.password !== form.password_confirmation) {
    toast.error('Heslá sa nezhodujú')
    return
  }

  try {
    await authStore.register({
      name: form.name,
      email: form.email,
      password: form.password,
      password_confirmation: form.password_confirmation,
      youtube_channel: form.youtube_channel || null,
      subscribers_count: form.subscribers_count || 0,
    })
    
    router.push('/dashboard')
  } catch (error) {
    // Error handling is done in the store
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
