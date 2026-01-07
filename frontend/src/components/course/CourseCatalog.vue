<template>
  <div class="space-y-4">
    <!-- Filter Bar -->
    <div class="mb-8 space-y-4">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h2 class="text-2xl md:text-3xl font-black text-white">Všetky kurzy</h2>
        
        <!-- Search Input -->
        <div class="relative flex-grow md:max-w-md">
          <input 
            v-model="searchQuery"
            @input="handleSearch"
            type="text" 
            placeholder="Hľadať kurzy..." 
            class="w-full bg-dark-900 text-white border border-dark-800 rounded-xl py-3 px-4 pl-10 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all placeholder-dark-400"
          >
          <svg class="w-5 h-5 text-dark-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </div>
      </div>

      <!-- Filters & Sort -->
      <div class="flex flex-col md:flex-row gap-4 items-center justify-between bg-dark-900 p-4 rounded-2xl border border-dark-800">
        <div class="flex flex-wrap gap-3 w-full md:w-auto">
          <!-- Difficulty Filter -->
          <CustomSelect
            v-model="selectedDifficulty"
            :options="difficultyOptions"
            placeholder="Všetky úrovne"
            class="w-full sm:w-48"
            multiple
            @update:modelValue="handleFilterChange"
          />

          <!-- Category Filter -->
          <CustomSelect
            v-model="selectedCategory"
            :options="categoryOptions"
            placeholder="Všetky kategórie"
            class="w-full sm:w-48"
            multiple
            @update:modelValue="handleFilterChange"
          />

          <!-- Price Filter -->
          <PriceFilter
            v-model="selectedPriceRange"
            :max="300"
            class="w-full sm:w-48"
            @update:modelValue="handleFilterChange"
          />

          <button
            @click="resetFilters"
            class="text-sm font-bold transition-all ml-2"
            :class="hasActiveFilters ? 'text-primary-500 hover:text-primary-400 cursor-pointer' : 'text-dark-500 cursor-not-allowed'"
            :disabled="!hasActiveFilters"
          >
            Resetovať filtre
          </button>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
          <span class="text-sm text-dark-400 font-medium whitespace-nowrap hidden md:block">Triediť podľa:</span>
          <!-- Sort -->
          <CustomSelect
            v-model="selectedSort"
            :options="sortOptions"
            placeholder="Zoradiť podľa"
            class="w-full sm:w-48"
            @update:modelValue="handleFilterChange"
          />
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-4 border-primary-500"></div>
    </div>

    <!-- Course Grid -->
    <div v-else-if="courses && courses.length > 0">
      <TransitionGroup 
        tag="div" 
        name="course-list" 
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 relative"
      >
        <div
          v-for="course in courses"
          :key="`course-${course.id}`"
          class="h-full"
        >
          <CourseCard
            :course="course"
            :is-selected="selectedCourseId === course.id"
            @click="handleCourseClick"
            @purchase="handlePurchase"
          />
        </div>
      </TransitionGroup>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="mt-12 flex justify-center space-x-2">
        <button 
          @click="changePage(pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          class="px-4 py-2 rounded-lg bg-dark-800 text-white disabled:opacity-50 hover:bg-dark-700 transition-colors border border-dark-700"
        >
          Predchádzajúca
        </button>
        <span class="px-4 py-2 text-dark-400">
          Strana {{ pagination.current_page }} z {{ pagination.last_page }}
        </span>
        <button 
          @click="changePage(pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          class="px-4 py-2 rounded-lg bg-dark-800 text-white disabled:opacity-50 hover:bg-dark-700 transition-colors border border-dark-700"
        >
          Ďalšia
        </button>
      </div>
    </div>

    <!-- No courses found -->
    <div v-else class="text-center py-20 bg-dark-900 rounded-2xl border border-dark-800 mt-8">
      <svg class="mx-auto h-16 w-16 text-dark-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
      <div class="text-dark-300 text-xl font-medium mb-2">Nenašli sa žiadne kurzy</div>
      <p class="text-dark-500">Skúste zmeniť kritériá vyhľadávania alebo filtre.</p>
      <button 
        @click="resetFilters"
        class="mt-6 px-6 py-2 bg-dark-800 hover:bg-dark-700 text-white rounded-lg transition-colors border border-dark-700"
      >
        Vymazať filtre
      </button>
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
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { debounce } from 'lodash'
import { useToast } from 'vue-toastification'
import { useAuthStore } from '@/stores/auth'
import { useCourseStore } from '@/stores/course'
import { useEnrollmentStore } from '@/stores/enrollment'
import { paymentService } from '@/services'

import CustomSelect from '@/components/ui/CustomSelect.vue'
import PriceFilter from '@/components/ui/PriceFilter.vue'
import CheckoutLoadingModal from '@/components/ui/CheckoutLoadingModal.vue'
import CourseCard from '@/components/course/CourseCard.vue'

const props = defineProps({
  selectedCourseId: {
    type: [Number, String],
    default: null
  }
})

const emit = defineEmits(['select', 'purchase'])

const router = useRouter()
const authStore = useAuthStore()
const courseStore = useCourseStore()
const enrollmentStore = useEnrollmentStore()
const toast = useToast()

// Store state
const courses = computed(() => courseStore.courses)
const loading = computed(() => courseStore.loading)
const pagination = computed(() => courseStore.pagination)
const categories = computed(() => courseStore.categories)

// Local state for checkout
const showCheckoutLoading = ref(false)
const checkoutCourse = ref(null)

// Filters state
const searchQuery = ref('')
const selectedDifficulty = ref([])
const selectedPriceRange = ref(null)
const selectedCategory = ref([])
const selectedSort = ref('created_at_desc')

// Options
const difficultyOptions = [
  { value: 'beginner', label: 'Začiatočník' },
  { value: 'intermediate', label: 'Pokročilý' },
  { value: 'advanced', label: 'Expert' }
]

const sortOptions = [
  { value: 'created_at_desc', label: 'Najnovšie' },
  { value: 'created_at_asc', label: 'Najstaršie' },
  { value: 'price_asc', label: 'Najlacnejšie' },
  { value: 'price_desc', label: 'Najdrahšie' },
]

const categoryOptions = computed(() => {
  const opts = []
  if (categories.value) {
    opts.push(...categories.value.map(c => ({
      value: c.slug,
      label: c.name,
      count: c.courses_count
    })))
  }
  return opts
})

const hasActiveFilters = computed(() => {
  return searchQuery.value || selectedDifficulty.value.length > 0 || (selectedPriceRange.value !== null) || selectedCategory.value.length > 0 || selectedSort.value !== 'created_at_desc'
})

// Handlers
const handleCourseClick = (course) => {
  emit('select', course)
}

const handleSearch = debounce(() => {
  applyFilters()
}, 500)

const handleFilterChange = () => {
  applyFilters()
}

const applyFilters = () => {
  const filters = {
    search: searchQuery.value,
  }

  filters.difficulty = selectedDifficulty.value
  filters.category = selectedCategory.value

  if (selectedPriceRange.value) {
    filters.min_price = selectedPriceRange.value.min
    filters.max_price = selectedPriceRange.value.max
  } else {
    filters.min_price = null
    filters.max_price = null
  }

  if (selectedSort.value) {
    filters.sort_by = selectedSort.value === 'created_at_desc' || selectedSort.value === 'created_at_asc' ? 'created_at' : 
                      selectedSort.value === 'price_asc' || selectedSort.value === 'price_desc' ? 'price' :
                      selectedSort.value === 'difficulty_asc' || selectedSort.value === 'difficulty_desc' ? 'difficulty_level' :
                      selectedSort.value === 'title_asc' ? 'title' : 'created_at'
                      
    filters.sort_order = selectedSort.value.includes('_asc') ? 'asc' : 'desc'
  }

  courseStore.updateFilters(filters)
}

const resetFilters = () => {
  searchQuery.value = ''
  selectedDifficulty.value = []
  selectedPriceRange.value = null
  selectedCategory.value = []
  selectedSort.value = 'created_at_desc'
  courseStore.clearFilters()
}

const changePage = (page) => {
  courseStore.fetchCourses({ page })
  // Scroll to top of grid
  const gridElement = document.querySelector('.grid')
  if (gridElement) {
    const top = gridElement.offsetTop - 100
    window.scrollTo({ top, behavior: 'smooth' })
  }
}

const handlePurchase = async (course) => {
  if (!authStore.user) {
    toast.info('Pre nákup kurzu sa musíte prihlásiť.')
    router.push('/login')
    return
  }

  const isPurchased = enrollmentStore.hasPurchasedCourse(course.id)
  
  if (isPurchased) {
    toast.info('Tento kurz už máte zakúpený a nachádza sa v sekcii "Moje kurzy".')
    return
  }

  try {
    checkoutCourse.value = course
    showCheckoutLoading.value = true
    
    const successUrl = `${window.location.origin}/payment/success?session_id={CHECKOUT_SESSION_ID}`
    const cancelUrl = `${window.location.origin}/courses?cancelled=true`
    const response = await paymentService.createCheckoutSession(course.id, successUrl, cancelUrl)
    
    if (response.checkout_url) {
      window.location.href = response.checkout_url
    } else {
      throw new Error('No checkout URL received')
    }
  } catch (error) {
    console.error('Error creating checkout session:', error)
    
    showCheckoutLoading.value = false
    checkoutCourse.value = null
    
    if (import.meta.env.DEV) {
       console.log('Falling back to simulator checkout')
       const checkoutUrl = `/checkout?courseTitle=${encodeURIComponent(course.title)}&coursePrice=${course.price}&courseId=${course.id}&courseSlug=${encodeURIComponent(course.slug)}`
       router.push(checkoutUrl)
    }
  }
}

const loadData = async () => {
    // If courses are already loaded (e.g. from visiting /courses before), we might not need to fetch.
    // However, to ensure fresh data and proper initialization (categories), we fetch.
    if (courseStore.courses.length === 0) {
        await courseStore.fetchCourses()
    }
    if (courseStore.categories.length === 0) {
        await courseStore.fetchCategories()
    }
    
    if (authStore.user) {
        // Load purchase status for visible courses
        // This is a bit inefficient if we do it every time, but necessary for correct button state
        for (const course of courses.value) {
           enrollmentStore.checkCoursePurchaseStatus(course.id)
        }
    }
}

onMounted(() => {
  loadData()
})
</script>

<style scoped>
/* List transitions */
.course-list-enter-active,
.course-list-leave-active {
  transition: all 0.5s ease;
}
.course-list-enter-from,
.course-list-leave-to {
  opacity: 0;
  transform: translateY(30px);
}
.course-list-move {
  transition: transform 0.5s ease;
}
</style>
