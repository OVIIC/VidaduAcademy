<template>
  <div class="min-h-screen bg-white">
    <!-- Loading -->
    <div v-if="loading" class="flex justify-center items-center min-h-screen">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
    </div>

    <!-- Course Details -->
    <div v-else-if="course" class="max-w-7xl mx-auto">
      <!-- Hero Section -->
      <div class="bg-gradient-to-r from-primary-600 to-secondary-600 text-white">
        <div class="px-4 sm:px-6 lg:px-8 py-16">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <div>
              <h1 class="text-4xl font-bold mb-4">{{ course.title }}</h1>
              <p class="text-xl text-white mb-6">{{ course.description }}</p>
              
              <div class="flex items-center space-x-6 mb-6">
                <div class="flex items-center">
                  <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  {{ course.lessons?.length || 0 }} Lessons
                </div>
                <div class="flex items-center">
                  <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  {{ formatDuration(course.duration) }}
                </div>
              </div>

              <div class="flex items-center space-x-4">
                <span class="text-3xl font-bold">${{ course.price }}</span>
                <button
                  v-if="!isEnrolled"
                  @click="handleEnrollment"
                  :disabled="purchasing"
                  class="bg-yellow-500 hover:bg-yellow-600 disabled:opacity-50 text-black font-semibold px-8 py-3 rounded-lg transition duration-200"
                >
                  {{ purchasing ? 'Processing...' : 'Enroll Now' }}
                </button>
                <router-link
                  v-else
                  :to="{ name: 'learn', params: { courseId: course.id } }"
                  class="bg-green-500 hover:bg-green-600 text-white font-semibold px-8 py-3 rounded-lg transition duration-200"
                >
                  Continue Learning
                </router-link>
              </div>
            </div>

            <!-- Course Preview/Thumbnail -->
            <div class="lg:pl-8">
              <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6">
                <div class="aspect-video bg-gray-800 rounded-lg flex items-center justify-center mb-4">
                  <svg class="w-16 h-16 text-white/50" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8 12.5l5-3-5-3v6z"/>
                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16z"/>
                  </svg>
                </div>
                <h3 class="text-lg font-semibold mb-2">Course Preview</h3>
                <p class="text-sm text-white">Get a taste of what you'll learn</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Course Content -->
      <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Main Content -->
          <div class="lg:col-span-2">
            <!-- What you'll learn -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
              <h2 class="text-2xl font-bold mb-4">What You'll Learn</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-for="(point, index) in learningPoints" :key="index" class="flex items-start">
                  <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                  </svg>
                  <span class="text-gray-700">{{ point }}</span>
                </div>
              </div>
            </div>

            <!-- Course Curriculum -->
            <div class="bg-white rounded-lg shadow-sm p-6">
              <h2 class="text-2xl font-bold mb-4">Course Curriculum</h2>
              <div class="space-y-3">
                <div
                  v-for="(lesson, index) in course.lessons"
                  :key="lesson.id"
                  class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition duration-200"
                >
                  <div class="flex items-center justify-between">
                    <div class="flex items-center">
                      <span class="bg-primary-100 text-primary-800 text-sm font-medium px-2.5 py-0.5 rounded mr-3">
                        {{ index + 1 }}
                      </span>
                      <div>
                        <h3 class="font-medium text-gray-900">{{ lesson.title }}</h3>
                        <p class="text-sm text-gray-500">{{ lesson.description }}</p>
                      </div>
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ formatDuration(lesson.duration) }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="lg:col-span-1">
            <!-- Course Info -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
              <h3 class="text-lg font-semibold mb-4">Course Information</h3>
              <div class="space-y-3">
                <div class="flex justify-between">
                  <span class="text-gray-600">Category:</span>
                  <span class="font-medium">{{ course.category?.name }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Level:</span>
                  <span class="font-medium capitalize">{{ course.level || 'Beginner' }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Duration:</span>
                  <span class="font-medium">{{ formatDuration(course.duration) }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Lessons:</span>
                  <span class="font-medium">{{ course.lessons?.length || 0 }}</span>
                </div>
              </div>
            </div>

            <!-- Instructor Info -->
            <div class="bg-white rounded-lg shadow-sm p-6">
              <h3 class="text-lg font-semibold mb-4">Your Instructor</h3>
              <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full flex items-center justify-center text-white font-bold">
                  VA
                </div>
                <div class="ml-3">
                  <h4 class="font-medium">VidaduAcademy Team</h4>
                  <p class="text-sm text-gray-500">YouTube Growth Experts</p>
                </div>
              </div>
              <p class="text-sm text-gray-600">
                Our team of YouTube growth specialists has helped thousands of creators build successful channels and monetize their content.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Course not found -->
    <div v-else class="flex justify-center items-center min-h-screen">
      <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Course Not Found</h1>
        <p class="text-gray-600 mb-4">The course you're looking for doesn't exist.</p>
        <router-link to="/courses" class="text-primary-600 hover:text-primary-800">
          Browse All Courses
        </router-link>
      </div>
    </div>

    <!-- Auth Modal -->
    <AuthModal
      :isOpen="showAuthModal"
      :courseTitle="course?.title"
      :coursePrice="course?.price"
      @close="showAuthModal = false"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useCourseStore } from '@/stores/course'
import { useAuthStore } from '@/stores/auth'
import { useEnrollmentStore } from '@/stores/enrollment'
import { api } from '@/services/api'
import { paymentService } from '@/services'
import AuthModal from '@/components/auth/AuthModal.vue'

export default {
  name: 'CourseDetailView',
  components: {
    AuthModal
  },
  setup() {
    const route = useRoute()
    const router = useRouter()
    const courseStore = useCourseStore()
    const authStore = useAuthStore()
    const enrollmentStore = useEnrollmentStore()
    
    const loading = ref(false)
    const purchasing = ref(false)
    const course = ref(null)
    const showAuthModal = ref(false)

    const isEnrolled = computed(() => {
      if (!course.value?.id || !authStore.user) return false
      return enrollmentStore.isEnrolledInCourse(course.value.id)
    })

    const learningPoints = ref([
      'Build a successful YouTube channel from scratch',
      'Create engaging content that converts',
      'Master YouTube SEO and discoverability',
      'Monetize your channel effectively',
      'Analyze performance and optimize growth',
      'Build a loyal community of subscribers'
    ])

    const formatDuration = (minutes) => {
      if (!minutes) return '0 min'
      const hours = Math.floor(minutes / 60)
      const mins = minutes % 60
      return hours > 0 ? `${hours}h ${mins}m` : `${mins}m`
    }

    const loadCourse = async () => {
      loading.value = true
      try {
        const response = await api.get(`/courses/${route.params.id}`)
        course.value = response.data
      } catch (error) {
        console.error('Error loading course:', error)
        router.push('/courses')
      } finally {
        loading.value = false
      }
    }

    const handleEnrollment = async () => {
      if (!authStore.user) {
        showAuthModal.value = true
        return
      }

      purchasing.value = true
      try {
        // Create Stripe checkout session
        console.log('Creating Stripe checkout session for course:', course.value.id)
        const response = await paymentService.createCheckoutSession(course.value.id)
        
        if (response.checkout_url) {
          // Redirect to Stripe Checkout
          window.location.href = response.checkout_url
        } else {
          throw new Error('No checkout URL received')
        }
      } catch (error) {
        console.error('Error creating checkout session:', error)
        
        // Fallback to our simulator for development
        console.log('Falling back to simulator checkout')
        const checkoutUrl = `/checkout?courseTitle=${encodeURIComponent(course.value.title)}&coursePrice=${course.value.price}&courseId=${course.value.id}&courseSlug=${encodeURIComponent(course.value.slug)}`
        router.push(checkoutUrl)
      } finally {
        purchasing.value = false
      }
    }

    onMounted(() => {
      loadCourse()
    })

    return {
      loading,
      purchasing,
      course,
      showAuthModal,
      isEnrolled,
      learningPoints,
      formatDuration,
      handleEnrollment
    }
  }
}
</script>
