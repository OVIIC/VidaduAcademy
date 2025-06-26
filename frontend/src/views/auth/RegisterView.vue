<template>
  <div class="min-h-screen bg-white flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="flex justify-center">
        <div class="w-12 h-12 bg-gradient-to-r from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center">
          <span class="text-white font-bold text-lg">V</span>
        </div>
      </div>
      <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
        Vytvorte si účet
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Alebo
        <router-link to="/login" class="font-medium text-primary-600 hover:text-primary-500">
          sa prihláste do existujúceho účtu
        </router-link>
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form class="space-y-6" @submit.prevent="handleSubmit">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">
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
            <label for="youtube_channel" class="block text-sm font-medium text-gray-700">
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
            <label for="subscribers_count" class="block text-sm font-medium text-gray-700">
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
            <label for="password" class="block text-sm font-medium text-gray-700">
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
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
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
            <label for="agree-terms" class="ml-2 block text-sm text-gray-900">
              Súhlasím s
              <a href="#" class="text-primary-600 hover:text-primary-500">Podmienkami služby</a>
              a
              <a href="#" class="text-primary-600 hover:text-primary-500">Zásadami ochrany súkromia</a>
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

const router = useRouter()
const authStore = useAuthStore()

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
    alert('Heslá sa nezhodujú')
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
