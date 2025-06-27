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
      
      if (token && user) {
        this.token = token
        this.user = JSON.parse(user)
        this.isAuthenticated = true
      }
    },

    async login(credentials) {
      this.loading = true
      try {
        const data = await authService.login(credentials)
        
        this.user = data.user
        this.token = data.token
        this.isAuthenticated = true
        
        localStorage.setItem('token', data.token)
        localStorage.setItem('user', JSON.stringify(data.user))
        
        toast.success('Welcome back!')
        return data
      } catch (error) {
        const message = error.response?.data?.message || 'Login failed'
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
        
        toast.success('Account created successfully!')
        return data
      } catch (error) {
        const message = error.response?.data?.message || 'Registration failed'
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
        this.token = null
        this.isAuthenticated = false
        
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        
        toast.info('You have been logged out')
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
  },
})
