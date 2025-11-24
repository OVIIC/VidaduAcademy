import api from './api'
import { apiCache } from '@/utils/cache'

// Helper function for cached API calls
const cachedApiCall = async (cacheKey, apiCall, ttl = 5 * 60 * 1000) => {
  // Check if data is in cache
  if (apiCache.has(cacheKey)) {
    return apiCache.get(cacheKey)
  }

  // Make API call and cache result
  const response = await apiCall()
  apiCache.set(cacheKey, response, ttl)
  return response
}

export const authService = {
  async login(credentials) {
    const response = await api.post('/auth/login', credentials)
    return response.data
  },

  async register(userData) {
    const response = await api.post('/auth/register', userData)
    return response.data
  },

  async logout() {
    const response = await api.post('/auth/logout')
    return response.data
  },

  async logoutAll() {
    const response = await api.post('/auth/logout-all')
    return response.data
  },

  async getUser() {
    const response = await api.get('/user')
    return response.data
  },

  async getCertificate(enrollmentId) {
    const response = await api.get(`/user/certificate/${enrollmentId}/download`, {
      responseType: 'blob' // Important for handling binary/html data if we were doing PDF, but for HTML view we might just want to open window
    })
    return response.data
  },

  // Helper to get the URL for opening in new tab
  getCertificateUrl(enrollmentId) {
    return `${api.defaults.baseURL}/user/certificate/${enrollmentId}/download`
  }
}

export const courseService = {
  async getAllCourses(params = {}) {
    const cacheKey = apiCache.generateKey('/courses', params)
    return cachedApiCall(cacheKey, async () => {
      const response = await api.get('/courses', { params })
      return response.data
    }, 10 * 60 * 1000) // Cache for 10 minutes
  },

  async getCourse(slug) {
    const cacheKey = apiCache.generateKey(`/courses/${slug}`)
    return cachedApiCall(cacheKey, async () => {
      const response = await api.get(`/courses/${slug}`)
      return response.data
    }, 15 * 60 * 1000) // Cache for 15 minutes
  },

  async getFeaturedCourses() {
    const cacheKey = apiCache.generateKey('/courses/featured')
    return cachedApiCall(cacheKey, async () => {
      const response = await api.get('/courses/featured')
      return response.data
    }, 10 * 60 * 1000) // Cache for 10 minutes
  },

  async getCoursesByInstructor(instructorId) {
    const cacheKey = apiCache.generateKey(`/courses/instructor/${instructorId}`)
    return cachedApiCall(cacheKey, async () => {
      const response = await api.get(`/courses/instructor/${instructorId}`)
      return response.data
    }, 15 * 60 * 1000) // Cache for 15 minutes
  },
}

export const paymentService = {
  async createCheckoutSession(courseId, successUrl = null, cancelUrl = null) {
    const response = await api.post('/payments/checkout', {
      course_id: courseId,
      success_url: successUrl,
      cancel_url: cancelUrl,
    })
    return response.data
  },

  async getPurchaseHistory(params = {}) {
    const response = await api.get('/payments/history', { params })
    return response.data
  },

  async checkCoursePurchaseStatus(courseId) {
    try {
      const response = await api.get(`/payments/course/${courseId}/status`)
      return response.data
    } catch (error) {
      return { has_purchased: false, is_enrolled: false }
    }
  },

  async simulatePurchase(courseId) {
    const response = await api.post('/payments/simulate', { course_id: courseId })
    return response.data
  },
}

export const learningService = {
  async getEnrolledCourses(params = {}) {
    const response = await api.get('/learning/courses', { params })
    return response.data
  },

  async getCourseContent(courseSlug) {
    const response = await api.get(`/learning/course/${courseSlug}`)
    return response.data
  },

  async watchLesson(courseSlug, lessonSlug) {
    const response = await api.get(`/learning/course/${courseSlug}/lesson/${lessonSlug}`)
    return response.data
  },

  async updateProgress(courseSlug, lessonSlug, progressData) {
    const response = await api.post(
      `/learning/course/${courseSlug}/lesson/${lessonSlug}/progress`,
      progressData
    )
    return response.data
  },
}

export const enrollmentService = {
  async getMyCourses() {
    const response = await api.get('/my-courses')
    return response.data
  },

  async enrollInCourse(courseId) {
    const response = await api.post('/enroll-me', {
      course_id: courseId,
    })
    return response.data
  },

  async unenrollFromCourse(courseId) {
    const response = await api.delete('/enrollments/unenroll', {
      data: { course_id: courseId },
    })
    return response.data
  },

  async updateProgress(enrollmentId, progressData) {
    const response = await api.put(
      `/enrollments/${enrollmentId}/progress`,
      progressData
    )
    return response.data
  },
}
