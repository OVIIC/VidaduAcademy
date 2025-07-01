<template>
  <div class="min-h-screen bg-black text-white overflow-x-hidden">
    <!-- Hero Course Section (Disney+ style) -->
    <div class="relative h-[65vh] overflow-hidden">
      <!-- Background Image with Smooth Transition -->
      <div class="absolute inset-0">
        <div class="relative w-full h-full">
          <img 
            :key="selectedCourse?.id || 'default'"
            :src="selectedCourse?.thumbnail || '/api/placeholder/1920/1080'"
            :alt="selectedCourse?.title || 'Course'"
            class="w-full h-full object-cover transition-all duration-1000 ease-in-out"
          />
          <!-- Disney+ style gradient overlays -->
          <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/50 to-transparent"></div>
          <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
          <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-black to-transparent"></div>
        </div>
      </div>

      <!-- Hero Content -->
      <div class="relative z-10 h-full flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
          <div class="max-w-3xl">
            <!-- Course Title (Large Disney+ style) -->
            <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-6 leading-none tracking-tight">
              {{ selectedCourse?.title || 'Načítavajú sa kurzy...' }}
            </h1>

            <!-- Course Info Bar (Disney+ style) -->
            <div v-if="selectedCourse" class="flex items-center space-x-6 mb-6 text-lg font-medium">
              <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="text-white">{{ selectedCourse.instructor }}</span>
              </div>
              <div class="flex items-center space-x-2">
                <div class="w-3 h-3 rounded-full" :class="getDifficultyColor(selectedCourse.difficulty)"></div>
                <span class="capitalize text-white">{{ selectedCourse.difficulty }}</span>
              </div>
              <div class="text-gray-200">{{ selectedCourse.duration }}</div>
              <div class="text-green-400 font-bold">€{{ selectedCourse.price }}</div>
            </div>

            <!-- Course Description -->
            <p class="text-xl md:text-2xl leading-relaxed mb-8 text-gray-200 max-w-2xl font-light">
              {{ selectedCourse?.description || 'Vyberte kurz pre zobrazenie detailov.' }}
            </p>

            <!-- Action Buttons (Disney+ style) -->
            <div class="flex items-center space-x-4">
              <button
                v-if="selectedCourse"
                @click="handlePurchase(selectedCourse)"
                :disabled="showCheckoutLoading && checkoutCourse?.id === selectedCourse.id"
                class="bg-primary-500 hover:bg-primary-600 text-white disabled:opacity-50 px-10 py-4 rounded-2xl font-bold text-xl transition-all duration-200 flex items-center space-x-3 shadow-lg hover:shadow-xl"
              >
                <svg v-if="showCheckoutLoading && checkoutCourse?.id === selectedCourse.id" class="animate-spin w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5H7a2 2 0 01-2-2V9a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2z"/>
                </svg>
                <span>{{ showCheckoutLoading && checkoutCourse?.id === selectedCourse.id ? 'Spracováva sa...' : 'Kúpiť kurz' }}</span>
              </button>
              
              <button class="bg-gray-600/70 hover:bg-gray-600/90 text-white px-8 py-4 rounded-2xl font-bold text-xl transition-all duration-200 flex items-center space-x-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Viac info</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Fade Transition Section -->
    <div class="h-8 bg-gradient-to-b from-black to-black"></div>

    <!-- Course Gallery Section (Disney+ horizontal scroll) -->
    <div class="bg-black py-4">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="flex items-center justify-between mb-8">
          <h2 class="text-2xl md:text-3xl font-bold text-white">Všetky kurzy</h2>
          <button
            @click="refreshCourses"
            :disabled="refreshing"
            class="bg-gray-700 hover:bg-gray-600 disabled:opacity-50 text-white px-6 py-3 rounded-lg transition-colors flex items-center space-x-2 font-medium"
          >
            <svg class="w-5 h-5" :class="{ 'animate-spin': refreshing }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>{{ refreshing ? 'Obnovuje...' : 'Obnoviť' }}</span>
          </button>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center py-20">
          <div class="animate-spin rounded-full h-12 w-12 border-b-4 border-white"></div>
        </div>

        <!-- Disney+ Style Horizontal Scroll Gallery -->
        <div v-else-if="courses && courses.length > 0" class="relative group">
          <div 
            ref="scrollContainer"
            class="flex space-x-6 overflow-x-auto scrollbar-hide pb-6"
            style="scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none;"
            @scroll="updateScrollButtons"
          >
            <div
              v-for="course in courses"
              :key="`course-${course.id}`"
              @click="selectCourse(course)"
              :class="[
                'flex-shrink-0 w-80 cursor-pointer transition-all duration-500 ease-out transform hover:scale-105',
                selectedCourse?.id === course.id ? 'scale-105 ring-4 ring-white/50 shadow-2xl' : 'hover:scale-105'
              ]"
            >
              <!-- Course Card (Disney+ style) -->
              <div class="bg-gradient-to-br from-gray-900/30 via-secondary-900/20 to-gray-900/40 backdrop-blur-xl border border-gray-700/50 rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 hover:from-gray-900/40 hover:via-secondary-900/30 hover:to-gray-900/50 hover:border-gray-600/60">
                <!-- Course Thumbnail -->
                <div class="aspect-video overflow-hidden relative">
                  <img 
                    :src="course.thumbnail || '/api/placeholder/500/280'"
                    :alt="course.title"
                    class="w-full h-full object-cover transition-transform duration-500 hover:scale-110"
                  />
                  <!-- Price Badge -->
                  <div class="absolute top-4 right-4 bg-green-500/80 backdrop-blur-sm border border-green-400/50 text-white px-3 py-1 rounded-full text-sm font-bold">
                    €{{ course.price }}
                  </div>
                  <!-- Selected Indicator -->
                  <div v-if="selectedCourse?.id === course.id" class="absolute inset-0 bg-gradient-to-br from-white/5 via-secondary-500/10 to-white/5 backdrop-blur-sm flex items-center justify-center border border-white/20">
                    <div class="bg-gradient-to-br from-white/10 via-secondary-500/20 to-white/10 backdrop-blur-md rounded-full p-4 border border-white/30">
                      <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                      </svg>
                    </div>
                  </div>
                </div>
                
                <!-- Course Info -->
                <div class="p-6">
                  <h3 class="font-bold text-xl mb-3 text-white line-clamp-2">{{ course.title }}</h3>
                  <div class="flex items-center justify-between text-sm text-gray-300 mb-3">
                    <div class="flex items-center space-x-2">
                      <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                      </svg>
                      <span class="font-medium">{{ course.instructor }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <div class="w-2 h-2 rounded-full" :class="getDifficultyColor(course.difficulty)"></div>
                      <span class="capitalize font-medium">{{ course.difficulty }}</span>
                    </div>
                  </div>
                  <p class="text-gray-400 text-sm line-clamp-3 mb-4">{{ course.description }}</p>
                  <div class="flex items-center justify-between">
                    <span class="text-gray-400 text-sm">{{ course.duration }}</span>
                    <button 
                      @click.stop="handlePurchase(course)"
                      class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors"
                    >
                      Kúpiť
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Scroll Navigation Buttons -->
          <button
            v-if="canScrollLeft"
            @click="scrollLeft"
            class="absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-6 bg-black/80 hover:bg-black text-white p-4 rounded-full transition-all duration-200 hover:scale-110 opacity-0 group-hover:opacity-100 z-10"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>
          
          <button
            v-if="canScrollRight"
            @click="scrollRight"
            class="absolute right-0 top-1/2 transform -translate-y-1/2 translate-x-6 bg-black/80 hover:bg-black text-white p-4 rounded-full transition-all duration-200 hover:scale-110 opacity-0 group-hover:opacity-100 z-10"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
        </div>

        <!-- No courses found -->
        <div v-else class="text-center py-20">
          <div class="text-gray-400 text-2xl mb-4">Žiadne kurzy sa nenašli</div>
          <p class="text-gray-500 text-lg">Skúste sa neskôr vrátiť pre nové kurzy.</p>
        </div>
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
import { ref, onMounted, computed, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { courseService, paymentService } from '@/services'
import { useAuthStore } from '@/stores/auth'
import { useEnrollmentStore } from '@/stores/enrollment'
import { useCourseStore } from '@/stores/course'
import { usePerformance } from '@/utils/performanceMonitor'
import { useToast } from 'vue-toastification'
import CheckoutLoadingModal from '@/components/ui/CheckoutLoadingModal.vue'

const router = useRouter()
const authStore = useAuthStore()
const enrollmentStore = useEnrollmentStore()
const courseStore = useCourseStore()
const { measureAsync, logMemory } = usePerformance()
const toast = useToast()

// Use course store instead of local state
const courses = computed(() => courseStore.courses)
const loading = computed(() => courseStore.loading)
const refreshing = ref(false)
const showCheckoutLoading = ref(false)
const checkoutCourse = ref(null)

// Selected course for hero section (Disney+ style)
const selectedCourse = ref(null)

// Scroll container reference and scroll state
const scrollContainer = ref(null)
const canScrollLeft = ref(false)
const canScrollRight = ref(false)

// Course selection (smooth transition to hero)
const selectCourse = async (course) => {
  console.log('Selecting course:', course.title)
  selectedCourse.value = course
  
  // Smooth scroll to top to show the new hero
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
}

// Scroll functionality
const scrollLeft = () => {
  if (scrollContainer.value) {
    const scrollAmount = scrollContainer.value.clientWidth * 0.8
    scrollContainer.value.scrollBy({
      left: -scrollAmount,
      behavior: 'smooth'
    })
  }
}

const scrollRight = () => {
  if (scrollContainer.value) {
    const scrollAmount = scrollContainer.value.clientWidth * 0.8
    scrollContainer.value.scrollBy({
      left: scrollAmount,
      behavior: 'smooth'
    })
  }
}

// Update scroll button visibility
const updateScrollButtons = () => {
  if (scrollContainer.value) {
    const container = scrollContainer.value
    canScrollLeft.value = container.scrollLeft > 0
    canScrollRight.value = container.scrollLeft < (container.scrollWidth - container.clientWidth)
  }
}

// Difficulty color mapping
const getDifficultyColor = (difficulty) => {
  const colors = {
    beginner: 'bg-green-500',
    intermediate: 'bg-yellow-500',
    advanced: 'bg-red-500',
    expert: 'bg-purple-500'
  }
  return colors[difficulty?.toLowerCase()] || 'bg-gray-500'
}

const loadCourses = async () => {
  try {
    console.log('Loading courses via course store...')
    await measureAsync('Load Courses API', async () => {
      return await courseStore.fetchCourses()
    })
    console.log('Courses loaded:', courses.value.length)
    
    // Set first course as selected for hero if no course is selected
    if (courses.value.length > 0 && !selectedCourse.value) {
      selectedCourse.value = courses.value[0]
      console.log('Set first course as hero:', selectedCourse.value.title)
    }
    
    // Load purchase status for each course if user is authenticated
    if (authStore.user && courses.value.length > 0) {
      await measureAsync('Load Purchase Status', async () => {
        await loadPurchaseStatus()
      })
    }
    
    // Update scroll buttons after courses load
    await nextTick()
    updateScrollButtons()
    
    // Log memory usage after loading
    logMemory('After loading courses')
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
    console.log('User not authenticated, redirecting to login')
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
    
    console.log('Creating Stripe checkout session for course:', course.title)
    
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
    console.log('Falling back to simulator checkout')
    const checkoutUrl = `/checkout?courseTitle=${encodeURIComponent(course.title)}&coursePrice=${course.price}&courseId=${course.id}&courseSlug=${encodeURIComponent(course.slug)}`
    router.push(checkoutUrl)
  }
}

const refreshCourses = async () => {
  refreshing.value = true
  try {
    console.log('Refreshing courses via course store...')
    
    // Use course store refresh method
    await courseStore.refreshCourses()
    
    // Set first course as selected if no course is selected
    if (courses.value.length > 0 && !selectedCourse.value) {
      selectedCourse.value = courses.value[0]
    }
    
    // Reload purchase status if user is authenticated
    if (authStore.user && courses.value.length > 0) {
      await loadPurchaseStatus()
    }
    
    // Update scroll buttons
    await nextTick()
    updateScrollButtons()
    
    console.log('Courses refreshed successfully, total:', courses.value.length)
  } catch (error) {
    console.error('Error refreshing courses:', error)
  } finally {
    refreshing.value = false
  }
}

onMounted(() => {
  loadCourses()
  
  // Add scroll listener to update scroll buttons
  if (scrollContainer.value) {
    scrollContainer.value.addEventListener('scroll', updateScrollButtons)
  }
})
</script>

<style scoped>
/* Hide scrollbar for course gallery */
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

/* Line clamp utilities */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Smooth transitions for course selection */
.course-transition {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Disney+ style backdrop blur */
.backdrop-blur-sm {
  backdrop-filter: blur(8px);
}
</style>
