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
          <div class="max-w-3xl mt-16">
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

    <!-- Course Gallery Section -->
    <div class="bg-black py-4 min-h-screen">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filter Bar -->
        <div class="mb-8 space-y-4">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-2xl md:text-3xl font-bold text-white">Všetky kurzy</h2>
            
            <!-- Search Input -->
            <div class="relative flex-grow md:max-w-md">
              <input 
                v-model="searchQuery"
                @input="handleSearch"
                type="text" 
                placeholder="Hľadať kurzy..." 
                class="w-full bg-gray-900 text-white border border-gray-700 rounded-xl py-3 px-4 pl-10 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
              >
              <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
          </div>

          <!-- Filters & Sort -->
          <div class="flex flex-col md:flex-row gap-4 items-center justify-between bg-gray-900/50 p-4 rounded-xl border border-gray-800">
            <div class="flex flex-wrap gap-3 w-full md:w-auto">
              <!-- Difficulty Filter -->
              <select 
                v-model="selectedDifficulty"
                @change="handleFilterChange"
                class="bg-gray-800 text-white border border-gray-700 rounded-lg py-2 px-3 text-sm focus:ring-primary-500 focus:border-primary-500"
              >
                <option value="">Všetky úrovne</option>
                <option value="beginner">Začiatočník</option>
                <option value="intermediate">Pokročilý</option>
                <option value="advanced">Expert</option>
              </select>

              <!-- Price Filter -->
              <select 
                v-model="selectedPrice"
                @change="handleFilterChange"
                class="bg-gray-800 text-white border border-gray-700 rounded-lg py-2 px-3 text-sm focus:ring-primary-500 focus:border-primary-500"
              >
                <option value="">Všetky ceny</option>
                <option value="free">Zadarmo</option>
                <option value="paid">Platené</option>
              </select>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto justify-end">
              <span class="text-gray-400 text-sm">Zoradiť podľa:</span>
              <select 
                v-model="selectedSort"
                @change="handleFilterChange"
                class="bg-gray-800 text-white border border-gray-700 rounded-lg py-2 px-3 text-sm focus:ring-primary-500 focus:border-primary-500"
              >
                <option value="created_at_desc">Najnovšie</option>
                <option value="price_asc">Cena: Najnižšia</option>
                <option value="price_desc">Cena: Najvyššia</option>
                <option value="title_asc">Názov: A-Z</option>
              </select>

              <button
                @click="resetFilters"
                class="text-gray-400 hover:text-white text-sm underline ml-2"
                v-if="hasActiveFilters"
              >
                Resetovať
              </button>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center py-20">
          <div class="animate-spin rounded-full h-12 w-12 border-b-4 border-white"></div>
        </div>

        <!-- Course Grid -->
        <div v-else-if="courses && courses.length > 0">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div
              v-for="course in courses"
              :key="`course-${course.id}`"
              @click="selectCourse(course)"
              :class="[
                'cursor-pointer transition-all duration-300 transform hover:-translate-y-1',
                selectedCourse?.id === course.id ? 'ring-2 ring-primary-500 rounded-2xl' : ''
              ]"
            >
              <!-- Course Card -->
              <div class="bg-gray-900 rounded-2xl overflow-hidden shadow-lg border border-gray-800 hover:border-gray-700 h-full flex flex-col">
                <!-- Course Thumbnail -->
                <div class="aspect-video overflow-hidden relative">
                  <img 
                    :src="course.thumbnail || '/api/placeholder/500/280'"
                    :alt="course.title"
                    class="w-full h-full object-cover transition-transform duration-500 hover:scale-110"
                  />
                  <!-- Price Badge -->
                  <div class="absolute top-3 right-3 bg-black/70 backdrop-blur-sm text-white px-2 py-1 rounded-md text-xs font-bold border border-gray-700">
                    {{ course.price > 0 ? `€${course.price}` : 'Zadarmo' }}
                  </div>
                </div>
                
                <!-- Course Info -->
                <div class="p-5 flex-grow flex flex-col">
                  <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-gray-800 text-gray-300 border border-gray-700 capitalize">
                      {{ course.difficulty }}
                    </span>
                    <div class="flex items-center text-xs text-gray-400">
                      <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      {{ course.duration }}
                    </div>
                  </div>

                  <h3 class="font-bold text-lg mb-2 text-white line-clamp-2 group-hover:text-primary-400 transition-colors">{{ course.title }}</h3>
                  
                  <p class="text-gray-400 text-sm line-clamp-2 mb-4 flex-grow">{{ course.short_description || course.description }}</p>
                  
                  <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-800">
                    <div class="flex items-center space-x-2">
                      <div class="w-6 h-6 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-300">
                        {{ course.instructor.charAt(0) }}
                      </div>
                      <span class="text-xs text-gray-300 truncate max-w-[100px]">{{ course.instructor }}</span>
                    </div>
                    <button 
                      @click.stop="handlePurchase(course)"
                      class="text-primary-400 hover:text-primary-300 text-sm font-medium transition-colors"
                    >
                      Kúpiť
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="pagination.last_page > 1" class="mt-12 flex justify-center space-x-2">
            <button 
              @click="changePage(pagination.current_page - 1)"
              :disabled="pagination.current_page === 1"
              class="px-4 py-2 rounded-lg bg-gray-800 text-white disabled:opacity-50 hover:bg-gray-700 transition-colors"
            >
              Predchádzajúca
            </button>
            <span class="px-4 py-2 text-gray-400">
              Strana {{ pagination.current_page }} z {{ pagination.last_page }}
            </span>
            <button 
              @click="changePage(pagination.current_page + 1)"
              :disabled="pagination.current_page === pagination.last_page"
              class="px-4 py-2 rounded-lg bg-gray-800 text-white disabled:opacity-50 hover:bg-gray-700 transition-colors"
            >
              Ďalšia
            </button>
          </div>
        </div>

        <!-- No courses found -->
        <div v-else class="text-center py-20 bg-gray-900/30 rounded-2xl border border-gray-800 mt-8">
          <svg class="mx-auto h-16 w-16 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <div class="text-gray-300 text-xl font-medium mb-2">Nenašli sa žiadne kurzy</div>
          <p class="text-gray-500">Skúste zmeniť kritériá vyhľadávania alebo filtre.</p>
          <button 
            @click="resetFilters"
            class="mt-6 px-6 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-lg transition-colors"
          >
            Vymazať filtre
          </button>
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
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { paymentService } from '@/services'
import { useAuthStore } from '@/stores/auth'
import { useEnrollmentStore } from '@/stores/enrollment'
import { useCourseStore } from '@/stores/course'
import { usePerformance } from '@/utils/performanceMonitor'
import { useToast } from 'vue-toastification'
import CheckoutLoadingModal from '@/components/ui/CheckoutLoadingModal.vue'
import { debounce } from 'lodash'

const router = useRouter()
const authStore = useAuthStore()
const enrollmentStore = useEnrollmentStore()
const courseStore = useCourseStore()
const { measureAsync, logMemory } = usePerformance()
const toast = useToast()

// Use course store instead of local state
const courses = computed(() => courseStore.courses)
const loading = computed(() => courseStore.loading)
const pagination = computed(() => courseStore.pagination)
const showCheckoutLoading = ref(false)
const checkoutCourse = ref(null)

// Filters state
const searchQuery = ref('')
const selectedDifficulty = ref('')
const selectedPrice = ref('')
const selectedSort = ref('created_at_desc')

// Computed property to check if any filters are active
const hasActiveFilters = computed(() => {
  return searchQuery.value || selectedDifficulty.value || selectedPrice.value || selectedSort.value !== 'created_at_desc'
})

// Selected course for hero section (Disney+ style)
const selectedCourse = ref(null)

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

// Search handler with debounce
const handleSearch = debounce(() => {
  applyFilters()
}, 500)

// Filter change handler
const handleFilterChange = () => {
  applyFilters()
}

// Apply all filters
const applyFilters = () => {
  const filters = {
    search: searchQuery.value,
    difficulty: selectedDifficulty.value,
    sort_by: 'created_at', // Default
    sort_order: 'desc' // Default
  }

  // Handle price filter
  if (selectedPrice.value === 'free') {
    filters.max_price = 0
  } else if (selectedPrice.value === 'paid') {
    filters.min_price = 0.01
  }

  // Handle sort
  if (selectedSort.value) {
    // eslint-disable-next-line no-unused-vars
    const [field, direction] = selectedSort.value.split('_')
    
    if (selectedSort.value === 'created_at_desc') {
      filters.sort_by = 'created_at'
      filters.sort_order = 'desc'
    } else if (selectedSort.value === 'price_asc') {
      filters.sort_by = 'price'
      filters.sort_order = 'asc'
    } else if (selectedSort.value === 'price_desc') {
      filters.sort_by = 'price'
      filters.sort_order = 'desc'
    } else if (selectedSort.value === 'title_asc') {
      filters.sort_by = 'title'
      filters.sort_order = 'asc'
    }
  }

  courseStore.updateFilters(filters)
}

// Reset filters
const resetFilters = () => {
  searchQuery.value = ''
  selectedDifficulty.value = ''
  selectedPrice.value = ''
  selectedSort.value = 'created_at_desc'
  courseStore.clearFilters()
}

// Pagination handler
const changePage = (page) => {
  courseStore.fetchCourses({ page })
  // Scroll to top of grid (not top of page to keep hero visible)
  const gridTop = document.querySelector('.bg-black.py-4')?.offsetTop || 0
  window.scrollTo({ top: gridTop - 100, behavior: 'smooth' })
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

onMounted(() => {
  loadCourses()
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
