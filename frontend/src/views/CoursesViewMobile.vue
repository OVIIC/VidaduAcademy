<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
      <div class="container-responsive py-4 sm:py-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h1 class="heading-1">Všetky kurzy</h1>
            <p class="mt-1 sm:mt-2 text-sm sm:text-base text-gray-600">
              Objavte kurzy na rast vašeho YouTube kanála
            </p>
          </div>
          
          <!-- Search and Filters (Mobile) -->
          <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
            <!-- Search -->
            <div class="relative flex-1 sm:w-80">
              <input
                v-model="searchQuery"
                @input="handleSearch"
                type="text"
                placeholder="Hľadať kurzy..."
                class="input-field pl-10 pr-4"
              >
              <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
              <button
                v-if="searchQuery"
                @click="clearSearch"
                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
              >
                <XMarkIcon class="h-5 w-5" />
              </button>
            </div>
            
            <!-- Filter Button -->
            <button
              @click="showFilters = !showFilters"
              class="btn-secondary flex items-center justify-center sm:justify-start"
            >
              <AdjustmentsHorizontalIcon class="h-5 w-5 mr-2" />
              <span class="hidden sm:inline">Filtre</span>
            </button>
          </div>
        </div>
        
        <!-- Active Filters -->
        <div v-if="hasActiveFilters" class="mt-4 flex flex-wrap gap-2">
          <span
            v-for="filter in activeFilters"
            :key="filter.key"
            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800"
          >
            {{ filter.label }}
            <button
              @click="removeFilter(filter.key)"
              class="ml-2 text-primary-600 hover:text-primary-800"
            >
              <XMarkIcon class="h-3 w-3" />
            </button>
          </span>
          <button
            @click="clearAllFilters"
            class="text-xs text-gray-500 hover:text-gray-700 underline"
          >
            Vymazať všetko
          </button>
        </div>
      </div>
    </div>

    <!-- Filters Panel -->
    <div v-if="showFilters" class="bg-white border-b border-gray-200">
      <div class="container-responsive py-4">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
          <!-- Difficulty Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Obťažnosť</label>
            <select v-model="filters.difficulty" class="input-field text-sm">
              <option value="">Všetky</option>
              <option value="beginner">Začiatočník</option>
              <option value="intermediate">Pokročilý</option>
              <option value="advanced">Expert</option>
            </select>
          </div>
          
          <!-- Price Range -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cena</label>
            <select v-model="filters.priceRange" class="input-field text-sm">
              <option value="">Všetky</option>
              <option value="0-50">0€ - 50€</option>
              <option value="50-100">50€ - 100€</option>
              <option value="100-200">100€ - 200€</option>
              <option value="200+">200€+</option>
            </select>
          </div>
          
          <!-- Duration -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Dĺžka</label>
            <select v-model="filters.duration" class="input-field text-sm">
              <option value="">Všetky</option>
              <option value="0-2">0-2 hodiny</option>
              <option value="2-5">2-5 hodín</option>
              <option value="5-10">5-10 hodín</option>
              <option value="10+">10+ hodín</option>
            </select>
          </div>
          
          <!-- Sort -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Zoradiť</label>
            <select v-model="filters.sort" class="input-field text-sm">
              <option value="created_at">Najnovšie</option>
              <option value="title">Názov</option>
              <option value="price">Cena</option>
              <option value="difficulty">Obťažnosť</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Course Count and View Toggle -->
    <div class="bg-white border-b border-gray-200">
      <div class="container-responsive py-3">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-600">
            <span v-if="!loading">
              {{ totalCourses }} {{ totalCourses === 1 ? 'kurz' : 'kurzov' }} nájdených
            </span>
          </div>
          
          <!-- View Toggle -->
          <div class="hidden sm:flex items-center space-x-2">
            <button
              @click="viewMode = 'grid'"
              :class="viewMode === 'grid' ? 'text-primary-600' : 'text-gray-400'"
              class="p-2 rounded-md hover:bg-gray-100"
            >
              <Squares2X2Icon class="h-5 w-5" />
            </button>
            <button
              @click="viewMode = 'list'"
              :class="viewMode === 'list' ? 'text-primary-600' : 'text-gray-400'"
              class="p-2 rounded-md hover:bg-gray-100"
            >
              <ListBulletIcon class="h-5 w-5" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="container-responsive py-6">
      <!-- Loading -->
      <LoadingState 
        v-if="loading" 
        type="courses"
        :count="8"
        class="py-6"
      />

      <!-- Courses Grid -->
      <div 
        v-else-if="courses && courses.length > 0"
        :class="viewMode === 'grid' ? 'grid-responsive' : 'space-y-4'"
      >
        <CourseCardMobile
          v-for="course in courses"
          :key="course.id"
          :course="course"
          :enrollment-data="getEnrollmentData(course.id)"
          :class="viewMode === 'list' ? 'sm:flex sm:items-center sm:space-x-4' : ''"
        />
      </div>

      <!-- No courses found -->
      <div v-else class="text-center py-12">
        <div class="w-24 h-24 mx-auto mb-4 text-gray-300">
          <MagnifyingGlassIcon class="w-full h-full" />
        </div>
        <div class="heading-3 text-gray-400 mb-2">Žiadne kurzy sa nenašli</div>
        <p class="text-gray-500 text-sm sm:text-base mb-4">
          Skúste upraviť filtre alebo hľadacie podmienky.
        </p>
        <button
          @click="clearAllFilters"
          class="btn-secondary"
        >
          Vymazať filtre
        </button>
      </div>

      <!-- Load More Button -->
      <div v-if="hasMoreCourses && !loading" class="text-center mt-8">
        <button
          @click="loadMoreCourses"
          :disabled="loadingMore"
          class="btn-primary"
        >
          <span v-if="loadingMore" class="flex items-center">
            <div class="loading-spinner mr-2"></div>
            Načítanie...
          </span>
          <span v-else>Načítať viac kurzov</span>
        </button>
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
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { 
  MagnifyingGlassIcon, 
  XMarkIcon, 
  AdjustmentsHorizontalIcon,
  Squares2X2Icon,
  ListBulletIcon
} from '@heroicons/vue/24/outline'
import { courseService } from '@/services'
import { useAuthStore } from '@/stores/auth'
import { useEnrollmentStore } from '@/stores/enrollment'
import CourseCardMobile from '@/components/courses/CourseCardMobile.vue'
import LoadingState from '@/components/ui/LoadingState.vue'
import CheckoutLoadingModal from '@/components/ui/CheckoutLoadingModal.vue'
import { debounce } from '@/utils/helpers'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const enrollmentStore = useEnrollmentStore()

// Reactive state
const loading = ref(true)
const loadingMore = ref(false)
const courses = ref([])
const searchQuery = ref(route.query.search || '')
const showFilters = ref(false)
const viewMode = ref('grid')
const currentPage = ref(1)
const totalCourses = ref(0)
const showCheckoutLoading = ref(false)
const checkoutCourse = ref(null)

// Filters
const filters = ref({
  difficulty: route.query.difficulty || '',
  priceRange: route.query.price || '',
  duration: route.query.duration || '',
  sort: route.query.sort || 'created_at'
})

// Computed
const hasActiveFilters = computed(() => {
  return searchQuery.value || 
         filters.value.difficulty || 
         filters.value.priceRange || 
         filters.value.duration ||
         filters.value.sort !== 'created_at'
})

const activeFilters = computed(() => {
  const active = []
  
  if (searchQuery.value) {
    active.push({ key: 'search', label: `"${searchQuery.value}"` })
  }
  
  if (filters.value.difficulty) {
    const labels = {
      'beginner': 'Začiatočník',
      'intermediate': 'Pokročilý', 
      'advanced': 'Expert'
    }
    active.push({ key: 'difficulty', label: labels[filters.value.difficulty] })
  }
  
  if (filters.value.priceRange) {
    active.push({ key: 'priceRange', label: `Cena: ${filters.value.priceRange.replace('-', '€ - ')}€` })
  }
  
  if (filters.value.duration) {
    active.push({ key: 'duration', label: `Dĺžka: ${filters.value.duration.replace('-', '-')} hod` })
  }
  
  return active
})

const hasMoreCourses = computed(() => {
  return courses.value.length < totalCourses.value
})

// Methods
const loadCourses = async (append = false) => {
  try {
    if (!append) {
      loading.value = true
      currentPage.value = 1
    } else {
      loadingMore.value = true
      currentPage.value++
    }

    const params = {
      page: currentPage.value,
      search: searchQuery.value,
      difficulty: filters.value.difficulty,
      sort: filters.value.sort,
      direction: 'desc'
    }

    // Handle price range
    if (filters.value.priceRange) {
      const [min, max] = filters.value.priceRange.split('-')
      if (min) params.min_price = min
      if (max && max !== '+') params.max_price = max
    }

    const response = await courseService.getAllCourses(params)
    
    if (append) {
      courses.value = [...courses.value, ...response.data]
    } else {
      courses.value = response.data
    }
    
    totalCourses.value = response.total
  } catch (error) {
    console.error('Error loading courses:', error)
  } finally {
    loading.value = false
    loadingMore.value = false
  }
}

const handleSearch = debounce(() => {
  updateUrlParams()
  loadCourses()
}, 300)

const updateUrlParams = () => {
  const query = {}
  
  if (searchQuery.value) query.search = searchQuery.value
  if (filters.value.difficulty) query.difficulty = filters.value.difficulty
  if (filters.value.priceRange) query.price = filters.value.priceRange
  if (filters.value.duration) query.duration = filters.value.duration
  if (filters.value.sort !== 'created_at') query.sort = filters.value.sort
  
  router.replace({ query })
}

const clearSearch = () => {
  searchQuery.value = ''
  handleSearch()
}

const removeFilter = (key) => {
  if (key === 'search') {
    searchQuery.value = ''
  } else {
    filters.value[key] = ''
  }
  updateUrlParams()
  loadCourses()
}

const clearAllFilters = () => {
  searchQuery.value = ''
  filters.value = {
    difficulty: '',
    priceRange: '',
    duration: '',
    sort: 'created_at'
  }
  updateUrlParams()
  loadCourses()
}

const loadMoreCourses = () => {
  loadCourses(true)
}

const getEnrollmentData = (courseId) => {
  return enrollmentStore.enrollments.find(e => e.course_id === courseId)
}

// Watch filters
watch(() => filters.value, () => {
  updateUrlParams()
  loadCourses()
}, { deep: true })

// Lifecycle
onMounted(async () => {
  await Promise.all([
    loadCourses(),
    authStore.isAuthenticated ? enrollmentStore.fetchEnrollments() : Promise.resolve()
  ])
})
</script>

<style scoped>
/* Component specific styles */
.course-grid-transition {
  transition: all 0.3s ease;
}

/* Mobile improvements */
@media (max-width: 640px) {
  .container-responsive {
    padding-left: 1rem;
    padding-right: 1rem;
  }
}
</style>
