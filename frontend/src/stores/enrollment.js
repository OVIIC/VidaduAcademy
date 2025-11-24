import { defineStore } from 'pinia'
import { enrollmentService, paymentService } from '@/services'
import { apiCache } from '@/utils/cache'

export const useEnrollmentStore = defineStore('enrollment', {
  state: () => ({
    myCourses: [],
    loading: false,
    lastUpdated: null,
    coursePurchaseStatus: {}, // Cache for course purchase status
  }),

  getters: {
    inProgressCourses: (state) => {
      if (!state.myCourses || !Array.isArray(state.myCourses)) {
        return []
      }
      return state.myCourses.filter(course => {
        const progress = course.enrollment_data?.progress_percentage || 0
        return progress > 0 && progress < 100
      })
    },
    
    completedCourses: (state) => {
      if (!state.myCourses || !Array.isArray(state.myCourses)) {
        return []
      }
      return state.myCourses.filter(course => {
        const progress = course.enrollment_data?.progress_percentage || 0
        return progress >= 100
      })
    },
    
    notStartedCourses: (state) => {
      if (!state.myCourses || !Array.isArray(state.myCourses)) {
        return []
      }
      return state.myCourses.filter(course => {
        const progress = course.enrollment_data?.progress_percentage || 0
        return progress === 0
      })
    },

    isEnrolledInCourse: (state) => (courseId) => {
      if (!state.myCourses || !Array.isArray(state.myCourses)) {
        return false
      }
      return state.myCourses.some(course => course.id === courseId)
    },

    hasPurchasedCourse: (state) => (courseId) => {
      const status = state.coursePurchaseStatus[courseId]
      return status ? status.has_purchased : false
    },
  },

  actions: {
    async loadMyCourses(force = false) {
      // If we have recent data and not forcing refresh, skip loading
      if (!force && this.myCourses.length > 0 && this.lastUpdated) {
        const timeDiff = Date.now() - this.lastUpdated
        if (timeDiff < 60000) { // Less than 1 minute ago
          return this.myCourses
        }
      }

      this.loading = true
      try {
        console.log('Loading my courses from store...')
        const response = await enrollmentService.getMyCourses()
        console.log('Store - My courses response:', response)
        
        // Ensure response.data exists and is an array
        if (response && response.data && Array.isArray(response.data)) {
          this.myCourses = response.data
        } else {
          console.warn('Invalid response format, using empty array')
          this.myCourses = []
        }
        
        this.lastUpdated = Date.now()
        
        console.log('Store - My courses loaded:', this.myCourses.length)
        return this.myCourses
      } catch (error) {
        console.error('Store - Error loading my courses:', error)
        // Always use empty array on error to prevent crashes
        this.myCourses = []
        // For development, don't throw error
        if (import.meta.env.MODE !== 'development') {
          throw error
        }
        return this.myCourses
      } finally {
        this.loading = false
      }
    },

    async enrollInCourse(courseId) {
      try {
        console.log('Enrolling in course:', courseId)
        const response = await enrollmentService.enrollInCourse(courseId)
        console.log('Enrollment response:', response)
        
        // Refresh my courses after successful enrollment
        await this.loadMyCourses(true)
        
        return response
      } catch (error) {
        console.error('Error enrolling in course:', error)
        
        // Only throw error in production - for development, we might want to continue with fallback
        if (import.meta.env.MODE === 'production') {
          throw error
        }
        
        // In development, we can still throw to let the component handle fallback
        throw error
      }
    },

    async checkCoursePurchaseStatus(courseId) {
      const cacheKey = `purchase-status:${courseId}`
      
      // Check cache first
      if (apiCache.has(cacheKey)) {
        const cachedData = apiCache.get(cacheKey)
        this.coursePurchaseStatus[courseId] = cachedData
        return cachedData
      }

      try {
        const response = await paymentService.checkCoursePurchaseStatus(courseId)
        
        // Cache the result for 2 minutes (purchase status changes infrequently)
        apiCache.set(cacheKey, response, 2 * 60 * 1000)
        this.coursePurchaseStatus[courseId] = response
        return response
      } catch (error) {
        console.error('Error checking course purchase status:', error)
        const fallbackData = { has_purchased: false, is_enrolled: false }
        this.coursePurchaseStatus[courseId] = fallbackData
        return fallbackData
      }
    },

    addCourseToMyCourses(course) {
      // Add course to local state without API call (for development/testing)
      const existingIndex = this.myCourses.findIndex(c => c.id === course.id)
      if (existingIndex === -1) {
        this.myCourses.push({
          ...course,
          enrollment_data: {
            progress_percentage: 0,
            enrolled_at: new Date().toISOString(),
          }
        })
        console.log('Course added to local state:', course.title)
      }
    },

    clearCourses() {
      this.myCourses = []
      this.lastUpdated = null
    },
  },
})
