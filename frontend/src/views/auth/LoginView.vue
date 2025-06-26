<template>
  <div class="min-h-screen bg-white flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="flex justify-center">
        <div class="w-12 h-12 bg-gradient-to-r from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center">
          <span class="text-white font-bold text-lg">V</span>
        </div>
      </div>
      <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
        Prihláste sa do svojho účtu
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Alebo
        <router-link to="/register" class="font-medium text-primary-600 hover:text-primary-500">
          si vytvorte nový účet
        </router-link>
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form class="space-y-6" @submit.prevent="handleSubmit">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
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
            <label for="password" class="block text-sm font-medium text-gray-700">
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
                class="input-field"
              />
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
              <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                Zapamätať si ma
              </label>
            </div>

            <div class="text-sm">
              <a href="#" class="font-medium text-primary-600 hover:text-primary-500">
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
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
  email: '',
  password: '',
  remember: false,
})

const handleSubmit = async () => {
  try {
    await authStore.login({
      email: form.email,
      password: form.password,
    })
    
    const redirect = router.currentRoute.value.query.redirect || '/dashboard'
    router.push(redirect)
  } catch (error) {
    // Error handling is done in the store
  }
}
</script>
