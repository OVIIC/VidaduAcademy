<template>
  <div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Success Icon -->
      <div class="text-center">
        <div class="mx-auto h-20 w-20 bg-green-100 rounded-full flex items-center justify-center">
          <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
        </div>
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Payment Successful!</h2>
        <p class="mt-2 text-sm text-gray-600">
          Thank you for your purchase. You now have access to your course.
        </p>
      </div>

      <!-- Course Information -->
      <div v-if="course" class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Course Enrolled</h3>
        <div class="flex items-start space-x-4">
          <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div class="flex-1">
            <h4 class="text-base font-medium text-gray-900">{{ course.title }}</h4>
            <p class="text-sm text-gray-600 mt-1">{{ course.description }}</p>
            <div class="mt-3 flex items-center text-sm text-gray-500">
              <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              {{ course.lessons?.length || 0 }} lessons
              <span class="mx-2">•</span>
              <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              {{ formatDuration(course.duration) }}
            </div>
          </div>
        </div>
      </div>

      <!-- Next Steps -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">What's Next?</h3>
        <div class="space-y-4">
          <div class="flex items-start">
            <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mt-0.5">
              <span class="text-xs font-medium text-blue-600">1</span>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-900">Access Your Course</p>
              <p class="text-sm text-gray-600">Start learning immediately with full access to all course materials.</p>
            </div>
          </div>
          <div class="flex items-start">
            <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mt-0.5">
              <span class="text-xs font-medium text-blue-600">2</span>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-900">Track Your Progress</p>
              <p class="text-sm text-gray-600">Monitor your learning journey and complete lessons at your own pace.</p>
            </div>
          </div>
          <div class="flex items-start">
            <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mt-0.5">
              <span class="text-xs font-medium text-blue-600">3</span>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-900">Earn Your Certificate</p>
              <p class="text-sm text-gray-600">Complete all lessons to earn your course completion certificate.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="space-y-3">
        <router-link
          v-if="course"
          :to="{ name: 'learn', params: { courseId: course.id } }"
          class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200"
        >
          Start Learning Now
        </router-link>
        
        <router-link
          to="/my-courses"
          class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200"
        >
          View All My Courses
        </router-link>
        
        <router-link
          to="/dashboard"
          class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200"
        >
          Go to Dashboard
        </router-link>
      </div>

      <!-- Support Information -->
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="flex items-center">
          <svg class="w-5 h-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
          </svg>
          <div>
            <p class="text-sm font-medium text-gray-900">Need Help?</p>
            <p class="text-xs text-gray-600">
              Contact our support team if you have any questions about your purchase.
            </p>
          </div>
        </div>
      </div>

      <!-- Receipt Information -->
      <div v-if="paymentDetails" class="text-center text-sm text-gray-500">
        <p>Order #{{ paymentDetails.order_id || 'N/A' }}</p>
        <p>Payment ID: {{ paymentDetails.payment_id || 'N/A' }}</p>
        <p class="mt-2">A confirmation email has been sent to your registered email address.</p>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { api } from '@/services/api'

export default {
  name: 'PaymentSuccessView',
  setup() {
    const route = useRoute()
    const course = ref(null)
    const paymentDetails = ref(null)

    const formatDuration = (minutes) => {
      if (!minutes) return '0 min'
      const hours = Math.floor(minutes / 60)
      const mins = minutes % 60
      return hours > 0 ? `${hours}h ${mins}m` : `${mins}m`
    }

    const loadPaymentDetails = async () => {
      try {
        // Extract session_id from URL query params
        const sessionId = route.query.session_id
        
        if (sessionId) {
          // Verify payment and get course details
          const response = await api.get(`/payments/verify/${sessionId}`)
          course.value = response.data.course
          paymentDetails.value = response.data.payment
        } else {
          // If no session_id, try to get course from query params
          const courseId = route.query.course_id
          if (courseId) {
            const courseResponse = await api.get(`/courses/${courseId}`)
            course.value = courseResponse.data
          }
        }
      } catch (error) {
        console.error('Error loading payment details:', error)
        // Even if there's an error, we still show the success page
        // as the payment was successful according to Stripe
      }
    }

    onMounted(() => {
      loadPaymentDetails()
    })

    return {
      course,
      paymentDetails,
      formatDuration
    }
  }
}
</script>
