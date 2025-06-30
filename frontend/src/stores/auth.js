import { defineStore } from 'pinia'
import { authService } from '@/services'
import { useToast } from 'vue-toastification'

const toast = useToast()

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
    isAuthenticated: false,
    loading: false,
  }),

  getters: {
    isInstructor: (state) => state.user?.is_instructor || false,
    userInitials: (state) => {
      if (!state.user?.name) return ''
      return state.user.name
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .slice(0, 2)
    },
  },

  actions: {
    initializeAuth() {
      const token = localStorage.getItem('token')
      const user = localStorage.getItem('user')
      
      console.log('Initializing auth:', { hasToken: !!token, hasUser: !!user })
      
      if (token && user) {
        this.token = token
        this.user = JSON.parse(user)
        this.isAuthenticated = true
        console.log('Auth initialized successfully for user:', this.user?.email)
      } else {
        console.log('No valid auth data found in localStorage')
      }
    },

    async login(credentials) {
      this.loading = true
      try {
        console.log('Attempting login for:', credentials.email)
        const data = await authService.login(credentials)
        console.log('Login successful, received data:', { user: data.user?.email, hasToken: !!data.token })
        
        this.user = data.user
        this.token = data.token
        this.isAuthenticated = true
        
        localStorage.setItem('token', data.token)
        localStorage.setItem('user', JSON.stringify(data.user))
        
        console.log('Auth state updated, localStorage saved')
        toast.success('Vitajte späť!')
        return data
      } catch (error) {
        console.error('Login failed:', error)
        const message = error.response?.data?.message || 'Prihlásenie sa nepodarilo'
        toast.error(message)
        throw error
      } finally {
        this.loading = false
      }
    },

    async register(userData) {
      this.loading = true
      try {
        const data = await authService.register(userData)
        
        this.user = data.user
        this.token = data.token
        this.isAuthenticated = true
        
        localStorage.setItem('token', data.token)
        localStorage.setItem('user', JSON.stringify(data.user))
        
        toast.success('Účet bol úspešne vytvorený!')
        return data
      } catch (error) {
        const message = error.response?.data?.message || 'Registrácia sa nepodarila'
        toast.error(message)
        throw error
      } finally {
        this.loading = false
      }
    },

    async logout() {
      try {
        await authService.logout()
        console.log('Backend logout successful')
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        console.log('Clearing auth state and localStorage')
        this.user = null
        this.token = null
        this.isAuthenticated = false
        
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        
        toast.info('Boli ste odhlásený')
      }
    },

    async fetchUser() {
      if (!this.token) return
      
      try {
        const user = await authService.getUser()
        this.user = user
        localStorage.setItem('user', JSON.stringify(user))
      } catch (error) {
        console.error('Failed to fetch user:', error)
        this.logout()
      }
    },

    setUser(user) {
      this.user = user
      localStorage.setItem('user', JSON.stringify(user))
    },

    // Debug method for development testing
    debugLogin() {
      const mockUser = {
        id: 1,
        name: 'Test User',
        email: 'test@example.com',
        is_instructor: false
      }
      const mockToken = 'mock-token-' + Date.now()
      
      this.user = mockUser
      this.token = mockToken
      this.isAuthenticated = true
      
      localStorage.setItem('token', mockToken)
      localStorage.setItem('user', JSON.stringify(mockUser))
      
      console.log('Debug login activated with user:', mockUser)
    }
  },
})
