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
          :isCheckoutLoading="showCheckoutLoading && checkoutCourse?.id === course.id"
          @purchase="handlePurchase"
        />
      </div>

      <!-- No courses found -->
      <div v-else class="text-center py-12">
        <div class="text-gray-400 text-lg">Žiadne kurzy sa nenašli</div>
        <p class="text-gray-500 mt-2">Skúste sa neskôr vrátiť pre nové kurzy.</p>
      </div>
    </div>

    <!-- Checkout Loading Modal -->
    <CheckoutLoadingModal
      :show="showCheckoutLoading"
      :courseTitle="checkoutCourse?.title"
      :coursePrice="checkoutCourse?.price"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { courseService, paymentService } from '@/services'
import { useAuthStore } from '@/stores/auth'
import { useEnrollmentStore } from '@/stores/enrollment'
import { usePerformance } from '@/utils/performanceMonitor'
import CourseCard from '@/components/courses/CourseCard.vue'
import CheckoutLoadingModal from '@/components/ui/CheckoutLoadingModal.vue'

const router = useRouter()
const authStore = useAuthStore()
const enrollmentStore = useEnrollmentStore()
const { measureAsync, logMemory } = usePerformance()
const courses = ref([])
const loading = ref(false)
const showCheckoutLoading = ref(false)
const checkoutCourse = ref(null)

const loadCourses = async () => {
  loading.value = true
  try {
    console.log('Loading courses...')
    const response = await measureAsync('Load Courses API', async () => {
      return await courseService.getAllCourses()
    })
    console.log('API response:', response)
    courses.value = response.data || []
    console.log('Courses loaded:', courses.value.length)
    
    // Load purchase status for each course if user is authenticated
    if (authStore.user && courses.value.length > 0) {
      await measureAsync('Load Purchase Status', async () => {
        await loadPurchaseStatus()
      })
    }
    
    // Log memory usage after loading
    logMemory('After loading courses')
  } catch (error) {
    console.error('Error loading courses:', error)
    courses.value = []
  } finally {
    loading.value = false
  }
}

const loadPurchaseStatus = async () => {
  try {
    for (const course of courses.value) {
      await enrollmentStore.checkCoursePurchaseStatus(course.id)
    }
  } catch (error) {
    console.error('Error loading purchase status:', error)
  }
}

const handlePurchase = async (course) => {
  if (!authStore.user) {
    console.log('User not authenticated')
    return
  }

  // Check if course is already purchased
  const isPurchased = enrollmentStore.hasPurchasedCourse(course.id)
  
  if (isPurchased) {
    alert('Tento kurz už máte zakúpený a nachádza sa v sekcii "Moje kurzy".')
    return
  }

  try {
    // Show loading modal
    checkoutCourse.value = course
    showCheckoutLoading.value = true
    
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
    
    // Hide loading modal on error
    showCheckoutLoading.value = false
    checkoutCourse.value = null
    
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
