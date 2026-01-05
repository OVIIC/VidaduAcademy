<template>
  <div class="min-h-screen bg-dark-950 text-white overflow-x-hidden relative">
    <!-- Background Effects -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-[500px] -left-1/4 w-[1000px] h-[1000px] bg-primary-600/20 rounded-full blur-[120px] opacity-30 animate-pulse-slow"></div>
        <div class="absolute top-[40%] -right-1/4 w-[800px] h-[800px] bg-secondary-600/10 rounded-full blur-[100px] opacity-30 animate-pulse-slow delay-500"></div>
        <div class="absolute -bottom-[200px] -left-1/4 w-[800px] h-[800px] bg-primary-600/20 rounded-full blur-[120px] opacity-30 animate-pulse-slow delay-1000"></div>
        <div class="absolute top-0 left-0 w-full h-full bg-[url('/images/grid-pattern.svg')] bg-repeat opacity-[0.03]"></div>
    </div>

    <!-- Course Detail Hero Section -->
    <div class="relative z-10">
      <CourseDetail 
        :course="selectedCourse" 
        :loading="showCheckoutLoading && checkoutCourse?.id === selectedCourse?.id"
        @purchase="handlePurchase"
      >
          <!-- Navigation Arrows overlaid on the hero image in CourseView only -->
          <!-- We can't easily slot into the exact image container from here without slot support in CourseDetail. 
               Alternatively, we can place them absolutely here if we know the layout matches. 
               Or update CourseDetail to support navigation slots/props. 
               Let's update CourseDetail to handle next/prev internally or via slots? 
               Actually, simpler: put these buttons OVER the CourseDetail component using absolute positioning if z-index allows. 
          -->
          <div v-if="courses.length > 1" class="absolute inset-x-0 top-[37.5vh] -translate-y-1/2 z-20 px-4 pointer-events-none flex justify-between">
              <button
                @click="navigateCourse('prev')"
                class="w-12 h-12 rounded-xl bg-dark-900/40 hover:bg-dark-900/60 backdrop-blur-sm border border-white/30 hover:border-white text-white flex items-center justify-center transition-all duration-200 hover:scale-110 shadow-lg pointer-events-auto"
                :disabled="currentCourseIndex === 0"
                :class="{ 'opacity-30 cursor-not-allowed': currentCourseIndex === 0 }"
              >
                <svg class="w-6 h-6 drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
              </button>
              
              <button
                @click="navigateCourse('next')"
                class="w-12 h-12 rounded-xl bg-dark-900/40 hover:bg-dark-900/60 backdrop-blur-sm border border-white/30 hover:border-white text-white flex items-center justify-center transition-all duration-200 hover:scale-110 shadow-lg pointer-events-auto"
                :disabled="currentCourseIndex === courses.length - 1"
                :class="{ 'opacity-30 cursor-not-allowed': currentCourseIndex === courses.length - 1 }"
              >
                <svg class="w-6 h-6 drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </button>
          </div>
          
          <!-- Course Gallery Section using CourseCatalog -->
          <div class="py-12 mt-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <CourseCatalog 
                    :selected-course-id="selectedCourse?.id"
                    @select="selectCourse" 
                />
            </div>
          </div>
      </CourseDetail>
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
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { paymentService } from '@/services'
import { useAuthStore } from '@/stores/auth'
import { useEnrollmentStore } from '@/stores/enrollment'
import { useCourseStore } from '@/stores/course'
import { useToast } from 'vue-toastification'
import CheckoutLoadingModal from '@/components/ui/CheckoutLoadingModal.vue'
import CourseCatalog from '@/components/course/CourseCatalog.vue'
import CourseDetail from '@/components/course/CourseDetail.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const enrollmentStore = useEnrollmentStore()
const courseStore = useCourseStore()
const toast = useToast()

// Use course store
const courses = computed(() => courseStore.courses)
// loading state is handled inside CourseCatalog for the grid, 
// but we might use it here if we wanted to show a global loader, 
// though CourseCatalog handles its own loading.
const showCheckoutLoading = ref(false)
const checkoutCourse = ref(null)

// Selected course for hero section (Disney+ style)
const selectedCourse = ref(null)

// Computed current course index
const currentCourseIndex = computed(() => {
  if (!selectedCourse.value || !courses.value.length) return -1
  return courses.value.findIndex(c => c.id === selectedCourse.value.id)
})

// Navigate to previous/next course
const navigateCourse = (direction) => {
  const currentIndex = currentCourseIndex.value
  if (currentIndex === -1) return
  
  let newIndex
  if (direction === 'prev') {
    newIndex = Math.max(0, currentIndex - 1)
  } else {
    newIndex = Math.min(courses.value.length - 1, currentIndex + 1)
  }
  
  if (newIndex !== currentIndex) {
    selectCourse(courses.value[newIndex])
  }
}

// Course selection (smooth transition to hero)
const selectCourse = async (course) => {
  if (import.meta.env.DEV) console.log('Selecting course:', course.title)
  selectedCourse.value = course
  
  // Smooth scroll to top to show the new hero
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
  
  // Update details if needed
  try {
     const fullCourse = await courseStore.fetchCourse(course.slug)
     selectedCourse.value = { ...selectedCourse.value, ...fullCourse }
  } catch (e) { console.error(e) }
}

const loadCourses = async () => {
  try {
    if (import.meta.env.DEV) console.log('Loading courses via course store...')
    
    await courseStore.fetchCourses()
    await courseStore.fetchCategories()
    
    if (import.meta.env.DEV) console.log('Courses loaded:', courses.value.length)
    
    // Check for query param to select specific course
    if (route.query.slug) {
      const found = courses.value.find(c => c.slug === route.query.slug)
      if (found) {
        selectedCourse.value = found
        if (import.meta.env.DEV) console.log('Selected course from query param:', found.title)
      }
    }
    
    // Set first course as selected for hero if no course is selected
    if (courses.value.length > 0 && !selectedCourse.value) {
      selectedCourse.value = courses.value[0]
      if (import.meta.env.DEV) console.log('Set first course as hero:', selectedCourse.value.title)
    }
    
    // Load purchase status for each course if user is authenticated
    if (authStore.user && courses.value.length > 0) {
      await loadPurchaseStatus()
    }
  } catch (error) {
    console.error('Error loading courses:', error)
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
    toast.info('Pre nákup kurzu sa musíte prihlásiť.')
    router.push('/login')
    return
  }

  // Check if course is already purchased
  const isPurchased = enrollmentStore.hasPurchasedCourse(course.id)
  
  if (isPurchased) {
    toast.info('Tento kurz už máte zakúpený a nachádza sa v sekcii "Moje kurzy".')
    return
  }

  try {
    // Show loading modal
    checkoutCourse.value = course
    showCheckoutLoading.value = true
    
    if (import.meta.env.DEV) console.log('Creating Stripe checkout session for course:', course.title)
    
    // Try to create Stripe checkout session
    const successUrl = `${window.location.origin}/payment/success?session_id={CHECKOUT_SESSION_ID}`
    const cancelUrl = `${window.location.origin}/courses?cancelled=true`
    const response = await paymentService.createCheckoutSession(course.id, successUrl, cancelUrl)
    
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
    if (import.meta.env.DEV) console.log('Falling back to simulator checkout')
    const checkoutUrl = `/checkout?courseTitle=${encodeURIComponent(course.title)}&coursePrice=${course.price}&courseId=${course.id}&courseSlug=${encodeURIComponent(course.slug)}`
    router.push(checkoutUrl)
  }
}

onMounted(() => {
  loadCourses()
})
</script>

<style scoped>
/* Animations shared */
@keyframes pulse-slow {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 0.2; transform: scale(1.1); }
}

.animate-pulse-slow {
  animation: pulse-slow 8s infinite ease-in-out;
}

.delay-500 { animation-delay: 0.5s; }
.delay-1000 { animation-delay: 1s; }
</style>
