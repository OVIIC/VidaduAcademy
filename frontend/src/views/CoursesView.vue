<template>
  <div class="min-h-screen bg-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Všetky kurzy</h1>
        <p class="mt-2 text-gray-600">Objavte kurzy na rast vašeho YouTube kanála</p>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
      </div>

      <!-- Courses Grid -->
      <div v-else-if="courses && courses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <CourseCard
          v-for="course in courses"
          :key="course.id"
          :course="course"
          @purchase="handlePurchase"
        />
      </div>

      <!-- No courses found -->
      <div v-else class="text-center py-12">
        <div class="text-gray-400 text-lg">Žiadne kurzy sa nenašli</div>
        <p class="text-gray-500 mt-2">Skúste sa neskôr vrátiť pre nové kurzy.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { courseService, paymentService } from '@/services'
import { useAuthStore } from '@/stores/auth'
import CourseCard from '@/components/courses/CourseCard.vue'

const router = useRouter()
const authStore = useAuthStore()
const courses = ref([])
const loading = ref(false)

const loadCourses = async () => {
  loading.value = true
  try {
    console.log('Loading courses...')
    const response = await courseService.getAllCourses()
    console.log('API response:', response)
    courses.value = response.data || []
    console.log('Courses loaded:', courses.value.length)
  } catch (error) {
    console.error('Error loading courses:', error)
    courses.value = []
  } finally {
    loading.value = false
  }
}

const handlePurchase = async (course) => {
  if (!authStore.user) {
    console.log('User not authenticated')
    return
  }

  try {
    console.log('Creating Stripe checkout session for course:', course.title)
    
    // Try to create Stripe checkout session
    const response = await paymentService.createCheckoutSession(course.id)
    
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
    const checkoutUrl = `/checkout?courseTitle=${encodeURIComponent(course.title)}&coursePrice=${course.price}&courseId=${course.id}&courseSlug=${encodeURIComponent(course.slug)}`
    router.push(checkoutUrl)
  }
}

onMounted(() => {
  loadCourses()
})
</script>
