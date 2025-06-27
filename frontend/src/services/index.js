import api from './api'

export const authService = {
  async login(credentials) {
    const response = await api.post('/login', credentials)
    return response.data
  },

  async register(userData) {
    const response = await api.post('/register', userData)
    return response.data
  },

  async logout() {
    const response = await api.post('/logout')
    return response.data
  },

  async getUser() {
    const response = await api.get('/user')
    return response.data
  },
}

export const courseService = {
  async getAllCourses(params = {}) {
    const response = await api.get('/courses', { params })
    return response.data
  },

  async getCourse(slug) {
    const response = await api.get(`/courses/${slug}`)
    return response.data
  },

  async getFeaturedCourses() {
    const response = await api.get('/courses/featured')
    return response.data
  },

  async getCoursesByInstructor(instructorId) {
    const response = await api.get(`/courses/instructor/${instructorId}`)
    return response.data
  },
}

export const paymentService = {
  async createCheckoutSession(courseId, successUrl, cancelUrl) {
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
