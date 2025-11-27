<template>
  <div class="min-h-screen text-white flex flex-col justify-center py-12 sm:px-6 lg:px-8" style="background-color: #3B3157;">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="flex justify-center">
        <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-lg flex items-center justify-center">
          <span class="text-white font-bold text-lg">V</span>
        </div>
      </div>
      <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-white">
        Vytvorte si účet
      </h2>
      <p class="mt-2 text-center text-sm text-gray-300">
        Alebo
        <router-link to="/login" class="font-medium text-primary-500 hover:text-primary-400">
          sa prihláste do existujúceho účtu
        </router-link>
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="backdrop-blur-xl border border-dark-700/50 rounded-2xl py-8 px-4 shadow-xl sm:px-10" style="background-color: #625A79;">
        <!-- Social Login -->
        <div class="mb-6">
          <a
            :href="`${apiUrl}/auth/google/redirect`"
            class="w-full flex justify-center items-center px-4 py-2 border border-dark-600 rounded-xl shadow-sm text-sm font-medium text-white bg-dark-800 hover:bg-dark-700 transition-colors duration-200">
            <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg">
              <g transform="matrix(1, 0, 0, 1, 27.009001, -39.238998)">
                <path fill="#4285F4" d="M -3.264 51.509 C -3.264 50.719 -3.334 49.969 -3.454 49.239 L -14.754 49.239 L -14.754 53.749 L -8.284 53.749 C -8.574 55.229 -9.424 56.479 -10.684 57.329 L -10.684 60.329 L -6.824 60.329 C -4.564 58.239 -3.264 55.159 -3.264 51.509 Z"/>
                <path fill="#34A853" d="M -14.754 63.239 C -11.514 63.239 -8.804 62.159 -6.824 60.329 L -10.684 57.329 C -11.764 58.049 -13.134 58.489 -14.754 58.489 C -17.884 58.489 -20.534 56.379 -21.484 53.529 L -25.464 53.529 L -25.464 56.619 C -23.494 60.539 -19.444 63.239 -14.754 63.239 Z"/>
                <path fill="#FBBC05" d="M -21.484 53.529 C -21.734 52.809 -21.864 52.039 -21.864 51.239 C -21.864 50.439 -21.724 49.669 -21.484 48.949 L -21.484 45.859 L -25.464 45.859 C -26.284 47.479 -26.754 49.299 -26.754 51.239 C -26.754 53.179 -26.284 54.999 -25.464 56.619 L -21.484 53.529 Z"/>
                <path fill="#EA4335" d="M -14.754 43.989 C -12.984 43.989 -11.404 44.599 -10.154 45.789 L -6.734 42.369 C -8.804 40.429 -11.514 39.239 -14.754 39.239 C -19.444 39.239 -23.494 41.939 -25.464 45.859 L -21.484 48.949 C -20.534 46.099 -17.884 43.989 -14.754 43.989 Z"/>
              </g>
            </svg>
            Registrovať sa cez Google
          </a>
        </div>

        <form class="space-y-6" @submit.prevent="handleSubmit">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-200">
              Celé meno
            </label>
            <div class="mt-1">
              <input
                id="name"
                v-model="form.name"
                name="name"
                type="text"
                autocomplete="name"
                required
                class="input-field"
              />
            </div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-200">
              Emailová adresa
            </label>
            <div class="mt-1">
              <input
                id="email"
                v-model="form.email"
                name="email"
                type="email"
                autocomplete="email"
                required
                class="input-field"
              />
            </div>
          </div>

          <div>
            <label for="youtube_channel" class="block text-sm font-medium text-gray-200">
              URL YouTube kanála (voliteľné)
            </label>
            <div class="mt-1">
              <input
                id="youtube_channel"
                v-model="form.youtube_channel"
                name="youtube_channel"
                type="url"
                placeholder="https://youtube.com/@vaškanál"
                class="input-field"
              />
            </div>
          </div>

          <div>
            <label for="subscribers_count" class="block text-sm font-medium text-gray-200">
              Súčasný počet odberateľov (voliteľné)
            </label>
            <div class="mt-1">
              <input
                id="subscribers_count"
                v-model="form.subscribers_count"
                name="subscribers_count"
                type="number"
                min="0"
                placeholder="0"
                class="input-field"
              />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-200">
              Heslo
            </label>
            <div class="mt-1">
              <input
                id="password"
                v-model="form.password"
                name="password"
                type="password"
                autocomplete="new-password"
                required
                class="input-field"
              />
            </div>
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-200">
              Potvrdiť heslo
            </label>
            <div class="mt-1">
              <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                required
                class="input-field"
              />
            </div>
          </div>

          <div class="flex items-center">
            <input
              id="agree-terms"
              v-model="form.agreeTerms"
              name="agree-terms"
              type="checkbox"
              required
              class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
            />
            <label for="agree-terms" class="ml-2 block text-sm text-gray-200">
              Súhlasím s
              <a href="#" class="text-primary-500 hover:text-primary-400">Podmienkami služby</a>
              a
              <a href="#" class="text-primary-500 hover:text-primary-400">Zásadami ochrany súkromia</a>
            </label>
          </div>

          <div>
            <button
              type="submit"
              :disabled="authStore.loading"
              class="btn-primary w-full flex justify-center"
            >
              <span v-if="authStore.loading" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Vytváram účet...
              </span>
              <span v-else>Vytvoriť účet</span>
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
