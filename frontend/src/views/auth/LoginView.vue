<template>
  <div class="min-h-screen text-white flex flex-col justify-center py-12 sm:px-6 lg:px-8" style="background-color: #3B3157;">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="flex justify-center">
        <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-lg flex items-center justify-center">
          <span class="text-white font-bold text-lg">V</span>
        </div>
      </div>
      <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-white">
        Prihláste sa do svojho účtu
      </h2>
      <p class="mt-2 text-center text-sm text-gray-300">
        Alebo
        <router-link to="/register" class="font-medium text-primary-500 hover:text-primary-400">
          si vytvorte nový účet
        </router-link>
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="backdrop-blur-xl border border-dark-700/50 rounded-2xl py-8 px-4 shadow-xl sm:px-10" style="background-color: #585070;">
        <form class="space-y-6" @submit.prevent="handleSubmit">
          <!-- Security Notice -->
          <div v-if="securityNotice" class="bg-gradient-to-br from-yellow-500/20 via-yellow-600/20 to-yellow-500/20 backdrop-blur-xl border border-yellow-500/50 rounded-2xl p-4">
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

          <!-- Social Login -->
          <div class="mb-6">
            <a
              :href="`${apiUrl}/auth/google/redirect`"
              class="w-full flex justify-center items-center px-4 py-2 border border-gray-600 rounded-2xl shadow-sm text-sm font-medium text-white bg-gray-800 hover:bg-gray-700 transition-colors duration-200"
            >
              <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg">
                <g transform="matrix(1, 0, 0, 1, 27.009001, -39.238998)">
                  <path fill="#4285F4" d="M -3.264 51.509 C -3.264 50.719 -3.334 49.969 -3.454 49.239 L -14.754 49.239 L -14.754 53.749 L -8.284 53.749 C -8.574 55.229 -9.424 56.479 -10.684 57.329 L -10.684 60.329 L -6.824 60.329 C -4.564 58.239 -3.264 55.159 -3.264 51.509 Z"/>
                  <path fill="#34A853" d="M -14.754 63.239 C -11.514 63.239 -8.804 62.159 -6.824 60.329 L -10.684 57.329 C -11.764 58.049 -13.134 58.489 -14.754 58.489 C -17.884 58.489 -20.534 56.379 -21.484 53.529 L -25.464 53.529 L -25.464 56.619 C -23.494 60.539 -19.444 63.239 -14.754 63.239 Z"/>
                  <path fill="#FBBC05" d="M -21.484 53.529 C -21.734 52.809 -21.864 52.039 -21.864 51.239 C -21.864 50.439 -21.724 49.669 -21.484 48.949 L -21.484 45.859 L -25.464 45.859 C -26.284 47.479 -26.754 49.299 -26.754 51.239 C -26.754 53.179 -26.284 54.999 -25.464 56.619 L -21.484 53.529 Z"/>
                  <path fill="#EA4335" d="M -14.754 43.989 C -12.984 43.989 -11.404 44.599 -10.154 45.789 L -6.734 42.369 C -8.804 40.429 -11.514 39.239 -14.754 39.239 C -19.444 39.239 -23.494 41.939 -25.464 45.859 L -21.484 48.949 C -20.534 46.099 -17.884 43.989 -14.754 43.989 Z"/>
                </g>
              </svg>
              Prihlásiť sa cez Google
            </a>
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
                :disabled="loading"
                class="block w-full rounded-2xl border-gray-600 bg-gray-800/50 text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm backdrop-blur-sm"
                @input="clearSecurityNotice"
              />
            </div>
            <div v-if="errors.email" class="mt-1 text-sm text-red-600">
              {{ errors.email[0] }}
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
                autocomplete="current-password"
                required
                :disabled="loading"
                class="block w-full rounded-2xl border-gray-600 bg-gray-800/50 text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm backdrop-blur-sm"
                @input="clearSecurityNotice"
              />
            </div>
            <div v-if="errors.password" class="mt-1 text-sm text-red-600">
              {{ errors.password[0] }}
            </div>
          </div>

          <!-- Rate Limit Notice -->
          <div v-if="rateLimitNotice" class="bg-gradient-to-br from-red-500/20 via-red-600/20 to-red-500/20 backdrop-blur-xl border border-red-500/50 rounded-2xl p-4">
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
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
              />
              <label for="remember-me" class="ml-2 block text-sm text-gray-200">
                Zapamätať si ma
              </label>
            </div>

            <div class="text-sm">
              <a href="#" class="font-medium text-primary-500 hover:text-primary-400">
                Zabudli ste heslo?
              </a>
            </div>
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
                Prihlasovanie...
              </span>
              <span v-else>Prihlásiť sa</span>
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

const apiUrl = import.meta.env.VITE_API_URL

const authStore = useAuthStore()
const { 
  form, 
  loading, 
  errors, 
  securityNotice, 
  rateLimitNotice, 
  clearSecurityNotice, 
  handleLogin 
} = useAuthLogic()

const handleSubmit = handleLogin
</script>
