import { defineStore } from 'pinia'
import { authService } from '@/services'
import { useToast } from 'vue-toastification'

const toast = useToast()

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    roles: [],
    isAuthenticated: false,
    loading: false,
  }),

  getters: {
    isInstructor: (state) => state.user?.is_instructor || state.roles.includes('instructor') || false,
    isAdmin: (state) => state.roles.includes('admin') || false,
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
    async initializeAuth() {
      // Check if we have a user in local storage to optimistically set state
      const user = localStorage.getItem('user')
      const roles = localStorage.getItem('roles')
      
      if (user) {
        this.user = JSON.parse(user)
        this.roles = roles ? JSON.parse(roles) : []
        this.isAuthenticated = true
      }

      // Verify session with backend
      try {
        await this.fetchUser()
      } catch (error) {
        // If fetchUser fails (401), we are not authenticated
        this.user = null
        this.roles = []
        this.isAuthenticated = false
        localStorage.removeItem('user')
        localStorage.removeItem('roles')
      }
    },

    async login(credentials) {
      this.loading = true
      try {
        // Get CSRF cookie first
        await authService.getCsrfCookie()
        
        const data = await authService.login(credentials)
        
        this.user = data.user
        this.roles = data.roles || []
        this.isAuthenticated = true
        
        localStorage.setItem('user', JSON.stringify(data.user))
        localStorage.setItem('roles', JSON.stringify(this.roles))
        
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
        this.roles = data.roles || []
        this.isAuthenticated = true
        
        localStorage.setItem('user', JSON.stringify(data.user))
        localStorage.setItem('roles', JSON.stringify(this.roles))
        
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
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.user = null
        this.roles = []
        this.isAuthenticated = false
        
        localStorage.removeItem('user')
        localStorage.removeItem('roles')
        
        toast.info('Boli ste odhlásený')
      }
    },

    async fetchUser() {
      if (!this.token) return
      
      try {
        const response = await authService.getUser()
        // Handle both direct user object or nested user object in response
        const user = response.user || response
        const roles = response.roles || []
        
        this.user = user
        this.roles = roles
        
        localStorage.setItem('user', JSON.stringify(user))
        localStorage.setItem('roles', JSON.stringify(roles))
      } catch (error) {
        console.error('Failed to fetch user:', error)
        this.logout()
      }
    },

    setUser(user) {
      this.user = user
      localStorage.setItem('user', JSON.stringify(user))
    },
    
    hasRole(role) {
      return this.roles.includes(role)
    },

    // Debug method for development testing
    debugLogin() {
      if (!import.meta.env.DEV) {
        console.warn('Debug login is not available in production')
        return
      }

      const mockUser = {
        id: 1,
        name: 'Test User',
        email: 'test@example.com',
        is_instructor: false
      }
      const mockToken = 'mock-token-' + Date.now()
      const mockRoles = ['admin'] // Mock admin role for testing
      
      this.user = mockUser
      this.token = mockToken
      this.roles = mockRoles
      this.isAuthenticated = true
      
      localStorage.setItem('token', mockToken)
      localStorage.setItem('user', JSON.stringify(mockUser))
      localStorage.setItem('roles', JSON.stringify(mockRoles))
      
      console.log('Debug login activated with user:', mockUser)
    }
  },
})

