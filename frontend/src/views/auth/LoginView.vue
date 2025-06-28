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
          <!-- Security Notice -->
          <div v-if="securityNotice" class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-yellow-700">{{ securityNotice }}</p>
              </div>
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
                :disabled="loading"
                class="input-field"
                @input="clearSecurityNotice"
              />
            </div>
            <div v-if="errors.email" class="mt-1 text-sm text-red-600">
              {{ errors.email[0] }}
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
                :disabled="loading"
                class="input-field"
                @input="clearSecurityNotice"
              />
            </div>
            <div v-if="errors.password" class="mt-1 text-sm text-red-600">
              {{ errors.password[0] }}
            </div>
          </div>

          <!-- Rate Limit Notice -->
          <div v-if="rateLimitNotice" class="bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-red-700">{{ rateLimitNotice }}</p>
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
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useSecurity } from '@/utils/security'

const router = useRouter()
const authStore = useAuthStore()
const { validateFormData, sanitizeText } = useSecurity()

// Form state
const form = reactive({
  email: '',
  password: '',
  remember: false
})

// Security and error states
const loading = ref(false)
const errors = ref({})
const securityNotice = ref('')
const rateLimitNotice = ref('')
const failedAttempts = ref(0)

// Clear security notices when user starts typing
const clearSecurityNotice = () => {
  securityNotice.value = ''
  rateLimitNotice.value = ''
  errors.value = {}
}

// Enhanced form submission with security validation
const handleSubmit = async () => {
  loading.value = true
  errors.value = {}
  
  try {
    // Sanitize inputs
    const sanitizedEmail = sanitizeText(form.email)
    const sanitizedPassword = form.password // Don't sanitize password content
    
    // Validate form data
    const validation = validateFormData({
      email: sanitizedEmail,
      password: sanitizedPassword
    }, {
      email: { required: true, email: true, maxLength: 255 },
      password: { required: true, minLength: 6, maxLength: 255 }
    })
    
    if (!validation.valid) {
      errors.value = validation.errors
      loading.value = false
      return
    }
    
    // Check for too many failed attempts
    if (failedAttempts.value >= 5) {
      rateLimitNotice.value = 'Príliš veľa neúspešných pokusov. Skúste to neskôr.'
      loading.value = false
      return
    }
    
    await authStore.login({
      email: sanitizedEmail,
      password: sanitizedPassword,
    })
    
    // Reset attempts on success
    failedAttempts.value = 0
    
    const redirect = router.currentRoute.value.query.redirect || '/dashboard'
    router.push(redirect)
    
  } catch (error) {
    loading.value = false
    failedAttempts.value++
    
    console.error('Login error:', error)
    
    if (error.response?.status === 429) {
      // Rate limit exceeded
      rateLimitNotice.value = error.response.data.message || 'Príliš veľa pokusov. Skúste to neskôr.'
      if (error.response.data.retry_after) {
        setTimeout(() => {
          rateLimitNotice.value = ''
        }, error.response.data.retry_after * 1000)
      }
    } else if (error.response?.status === 423) {
      // Account locked
      securityNotice.value = 'Účet je dočasne zablokovaný kvôli viacerým neúspešným pokusom.'
    } else if (error.response?.status === 403) {
      // Security block
      securityNotice.value = 'Prihlásenie bolo zablokované z bezpečnostných dôvodov.'
    } else if (error.response?.status === 422) {
      // Validation errors
      errors.value = error.response.data.errors || {}
    } else if (error.response?.status === 401) {
      // Invalid credentials
      errors.value = { 
        general: [error.response.data.message || 'Nesprávne prihlasovacie údaje.'] 
      }
      
      // Show security notice after multiple failed attempts
      if (failedAttempts.value >= 3) {
        securityNotice.value = 'Viacero neúspešných pokusov. Váš účet môže byť dočasne zablokovaný.'
      }
    } else {
      // Generic error
      errors.value = { 
        general: ['Nastala chyba pri prihlasovaní. Skúste to neskôr.'] 
      }
    }
  }
}
</script>
