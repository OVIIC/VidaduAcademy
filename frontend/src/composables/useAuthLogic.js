import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useSecurity } from '@/utils/security'

export function useAuthLogic() {
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
  const handleLogin = async () => {
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

  return {
    form,
    loading,
    errors,
    securityNotice,
    rateLimitNotice,
    clearSecurityNotice,
    handleLogin
  }
}
