import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// Request interceptor to add auth token
api.interceptors.request.use((config) => {
  const authStore = useAuthStore()
  const token = authStore.token
  
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
    console.log('Request with auth token to:', config.url)
  } else {
    console.log('Request without auth token to:', config.url)
  }
  
  return config
})

// Response interceptor to handle auth errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      console.log('401 error received from:', error.config?.url)
      
      // Don't logout if this is already a login/register request
      const isAuthRequest = error.config?.url?.includes('/login') || 
                           error.config?.url?.includes('/register')
      
      if (!isAuthRequest) {
        console.log('Logging out user due to 401 error')
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
