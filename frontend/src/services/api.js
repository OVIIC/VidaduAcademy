import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true,
})

// Request interceptor (Token is now handled via HttpOnly Cookies)
api.interceptors.request.use((config) => {
  if (import.meta.env.DEV) console.log('Request to:', config.url)
  return config
})

// Response interceptor to handle auth errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      if (import.meta.env.DEV) console.log('401 error received from:', error.config?.url)
      
      // Don't logout if this is already a login/register request
      const isAuthRequest = error.config?.url?.includes('/login') || 
                           error.config?.url?.includes('/register')
      
      if (!isAuthRequest) {
        if (import.meta.env.DEV) console.log('Logging out user due to 401 error')
        const authStore = useAuthStore()
        if (authStore.isAuthenticated) {
          authStore.logout()
        }
      }
    }
    return Promise.reject(error)
  }
)

export default api
export { api }
