<template>
  <div class="course-card group">
    <div class="relative overflow-hidden">
      <LazyImage
        :src="course.thumbnail || '/placeholder-course.jpg'"
        :alt="course.title"
        container-class="w-full h-40 sm:h-48"
        image-class="w-full h-40 sm:h-48 object-cover group-hover:scale-105 transition-transform duration-300"
        :fallback-src="'/placeholder-course.jpg'"
      />
      
      <!-- Featured Badge -->
      <div v-if="course.featured" class="absolute top-2 right-2 sm:top-3 sm:right-3">
        <div class="bg-yellow-400 text-yellow-900 px-2 py-1 rounded-full text-xs font-medium flex items-center">
          <StarIcon class="h-3 w-3 mr-1" />
          <span class="hidden sm:inline">Odporúčané</span>
        </div>
      </div>

      <!-- Duration Badge -->
      <div class="absolute bottom-2 right-2 bg-black bg-opacity-70 text-white px-2 py-1 rounded text-xs font-medium">
        {{ formatDuration(course.duration_minutes) }}
      </div>

      <!-- Difficulty Badge -->
      <div class="absolute top-2 left-2 sm:top-3 sm:left-3">
        <span 
          class="px-2 py-1 rounded-full text-xs font-medium"
          :class="getDifficultyColor(course.difficulty_level)"
        >
          {{ getDifficultyLabel(course.difficulty_level) }}
        </span>
      </div>
    </div>

    <div class="p-4 sm:p-6">
      <!-- Instructor -->
      <div v-if="course.instructor" class="flex items-center mb-3">
        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
          <span class="text-xs font-medium text-gray-600">
            {{ getInstructorInitials(course.instructor?.name) }}
          </span>
        </div>
        <div class="min-w-0 flex-1">
          <p class="text-sm font-medium text-gray-900 truncate">{{ course.instructor?.name }}</p>
          <p v-if="course.instructor?.subscribers_count" class="text-xs text-gray-500">
            {{ formatSubscribers(course.instructor?.subscribers_count) }} odberateľov
          </p>
        </div>
      </div>

      <!-- Course Title -->
      <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-primary-600 transition-colors leading-tight">
        {{ course.title }}
      </h3>

      <!-- Short Description -->
      <p class="text-gray-600 text-sm mb-4 line-clamp-2 leading-relaxed hidden sm:block">
        {{ course.short_description }}
      </p>

      <!-- Mobile Description (shorter) -->
      <p class="text-gray-600 text-sm mb-3 line-clamp-1 sm:hidden">
        {{ course.short_description }}
      </p>

      <!-- Course Meta -->
      <div class="flex items-center justify-between text-xs sm:text-sm text-gray-500 mb-4">
        <div class="flex items-center space-x-2 sm:space-x-4">
          <div class="flex items-center">
            <AcademicCapIcon class="h-3 w-3 sm:h-4 sm:w-4 mr-1" />
            <span class="hidden sm:inline">{{ getDifficultyLabel(course.difficulty_level) }}</span>
            <span class="sm:hidden">{{ getDifficultyLabel(course.difficulty_level).substring(0, 3) }}</span>
          </div>
          <div class="flex items-center">
            <CalendarIcon class="h-3 w-3 sm:h-4 sm:w-4 mr-1" />
            <span>{{ formatDate(course.created_at) }}</span>
          </div>
        </div>
      </div>

      <!-- Price and Action -->
      <div class="flex items-center justify-between">
        <div class="flex flex-col">
          <span class="text-lg sm:text-xl font-bold text-primary-600">
            €{{ course.price }}
          </span>
          <span class="text-xs text-gray-500">jednorazovo</span>
        </div>
        
        <button
          @click="handleCourseAction"
          :disabled="loading"
          class="btn-primary text-sm px-3 py-2 sm:px-4"
          :class="{ 'opacity-50 cursor-not-allowed': loading }"
        >
          <span v-if="loading" class="flex items-center">
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="hidden sm:inline">Načítanie...</span>
          </span>
          <span v-else-if="isEnrolled">
            <span class="hidden sm:inline">Pokračovať</span>
            <span class="sm:hidden">▶</span>
          </span>
          <span v-else>
            <span class="hidden sm:inline">Kúpiť kurz</span>
            <span class="sm:hidden">Kúpiť</span>
          </span>
        </button>
      </div>

      <!-- Progress Bar (if enrolled) -->
      <div v-if="isEnrolled && enrollmentData?.progress" class="mt-4">
        <div class="flex justify-between text-xs text-gray-600 mb-1">
          <span>Pokrok</span>
          <span>{{ Math.round(enrollmentData.progress) }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div 
            class="bg-primary-600 h-2 rounded-full transition-all duration-300"
            :style="{ width: `${enrollmentData.progress}%` }"
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { 
  StarIcon, 
  ClockIcon, 
  AcademicCapIcon, 
  CalendarIcon 
} from '@heroicons/vue/24/outline'
import LazyImage from '@/components/ui/LazyImage.vue'
import { useAuthStore } from '@/stores/auth'
import { courseService } from '@/services'

const props = defineProps({
  course: {
    type: Object,
    required: true
  },
  enrollmentData: {
    type: Object,
    default: null
  }
})

const router = useRouter()
const authStore = useAuthStore()
const loading = ref(false)

const isEnrolled = computed(() => {
  return props.enrollmentData !== null
})

const getInstructorInitials = (name) => {
  if (!name) return '?'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2)
}

const formatSubscribers = (count) => {
  if (!count || count === 0) return '0'
  if (count >= 1000000) {
    return `${(count / 1000000).toFixed(1)}M`
  } else if (count >= 1000) {
    return `${(count / 1000).toFixed(1)}K`
  }
  return count.toString()
}

const formatDuration = (minutes) => {
  if (!minutes) return '0 min'
  if (minutes >= 60) {
    const hours = Math.floor(minutes / 60)
    const remainingMinutes = minutes % 60
    if (remainingMinutes === 0) {
      return `${hours}h`
    }
    return `${hours}h ${remainingMinutes}m`
  }
  return `${minutes}m`
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('sk-SK', { 
    month: 'short', 
    year: 'numeric' 
  }).format(date)
}

const getDifficultyLabel = (level) => {
  const labels = {
    'beginner': 'Začiatočník',
    'intermediate': 'Pokročilý',
    'advanced': 'Expert'
  }
  return labels[level] || 'Začiatočník'
}

const getDifficultyColor = (level) => {
  const colors = {
    'beginner': 'bg-green-100 text-green-800',
    'intermediate': 'bg-yellow-100 text-yellow-800',
    'advanced': 'bg-red-100 text-red-800'
  }
  return colors[level] || 'bg-green-100 text-green-800'
}

const handleCourseAction = async () => {
  if (loading.value) return
  
  if (isEnrolled.value) {
    // Navigate to course study view
    router.push(`/learn/${props.course.slug}`)
  } else {
    if (!authStore.isAuthenticated) {
      router.push('/login')
      return
    }
    
    // Navigate to course detail or purchase
    router.push(`/courses/${props.course.slug}`)
  }
}
</script>

<style scoped>
/* Line clamping for text truncation */
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Custom course card styling */
.course-card {
  @apply bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden transition-all duration-300;
}

.course-card:hover {
  @apply shadow-lg;
  transform: translateY(-2px);
}

/* Disable hover effects on touch devices */
@media (hover: none) {
  .course-card:hover {
    transform: none;
    @apply shadow-sm;
  }
  
  .group-hover\:scale-105 {
    transform: none !important;
  }
}

/* Mobile touch improvements */
@media (max-width: 640px) {
  .course-card {
    @apply rounded-lg;
  }
  
  .btn-primary {
    @apply text-sm px-3 py-2;
    min-height: 40px;
  }
}
</style>
