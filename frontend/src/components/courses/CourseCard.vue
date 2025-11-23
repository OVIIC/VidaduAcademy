<template>
  <div class="card hover:shadow-lg transition-shadow duration-300 group">
    <div class="relative overflow-hidden">
      <LazyImage
        :src="course.thumbnail || '/placeholder-course.jpg'"
        :alt="course.title"
        container-class="w-full h-48"
        image-class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
        :fallback-src="'/placeholder-course.jpg'"
      />
      <div v-if="course.featured" class="absolute top-3 right-3">
        <StarIcon class="h-5 w-5 text-yellow-400 fill-current" />
      </div>
    </div>

    <div class="p-6">
      <!-- Instructor -->
      <div v-if="course.instructor" class="flex items-center mb-3">
        <div class="w-8 h-8 bg-dark-700 rounded-full flex items-center justify-center mr-3">
          <span class="text-xs font-medium text-dark-300">
            {{ getInstructorInitials(course.instructor?.name) }}
          </span>
        </div>
        <div>
          <p class="text-sm font-medium text-white">{{ course.instructor?.name }}</p>
          <p v-if="course.instructor?.subscribers_count" class="text-xs text-dark-400">
            {{ formatSubscribers(course.instructor?.subscribers_count) }} odberateľov
          </p>
        </div>
      </div>

      <!-- Course Title -->
      <h3 class="text-lg font-semibold text-white mb-2 line-clamp-2 group-hover:text-primary-500 transition-colors">
        {{ course.title }}
      </h3>

      <!-- Short Description -->
      <p class="text-dark-300 text-sm mb-4 line-clamp-2">
        {{ course.short_description }}
      </p>

      <!-- Course Meta -->
      <div class="flex items-center justify-between text-sm text-dark-400 mb-4">
        <div class="flex items-center space-x-4">
          <div class="flex items-center">
            <ClockIcon class="h-4 w-4 mr-1" />
            {{ formatDuration(course.duration_minutes) }}
          </div>
          <div class="flex items-center">
            <AcademicCapIcon class="h-4 w-4 mr-1" />
            Kurz
          </div>
        </div>
        <div class="flex items-center">
          <span
            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
            :class="getDifficultyBadgeClass(course.difficulty_level)"
          >
            {{ translateDifficulty(course.difficulty_level) }}
          </span>
        </div>
      </div>

      <!-- Price and Action -->
      <div class="flex items-center justify-between">
        <div>
          <span class="text-2xl font-bold text-white">
            {{ formatPrice(course.price) }}
          </span>
          <span class="text-sm text-dark-400 ml-1">{{ course.currency }}</span>
        </div>
        
        <div class="flex flex-col items-end space-y-2">
          <!-- Purchase status -->
          <div v-if="isPurchased" class="text-sm font-medium text-green-400 bg-green-900/30 px-2 py-1 rounded">
            Kurz je zakúpený
          </div>
          
          <button
            @click="showCourseDetail"
            class="btn-primary"
          >
            Zistiť viac
          </button>
        </div>
      </div>
    </div>

    <!-- Course Detail Modal -->
    <CourseDetailModal
      :show="showModal"
      :course="course"
      :isPurchased="isPurchased"
      :isCheckoutLoading="isCheckoutLoading"
      @close="showModal = false"
      @purchase="handlePurchase"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import {
  StarIcon,
  ClockIcon,
  AcademicCapIcon,
} from '@heroicons/vue/24/outline'
import { useEnrollmentStore } from '@/stores/enrollment'
import CourseDetailModal from './CourseDetailModal.vue'
import LazyImage from '@/components/ui/LazyImage.vue'

const props = defineProps({
  course: {
    type: Object,
    required: true,
  },
  isCheckoutLoading: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['purchase'])

const enrollmentStore = useEnrollmentStore()
const showModal = ref(false)

// Check if course is purchased
const isPurchased = computed(() => {
  return enrollmentStore.hasPurchasedCourse(props.course.id)
})

const showCourseDetail = () => {
  showModal.value = true
}

const handlePurchase = (course) => {
  emit('purchase', course)
}

const formatPrice = (price) => {
  if (!price) return '$0.00'
  const numPrice = parseFloat(price)
  return isNaN(numPrice) ? '$0.00' : `$${numPrice.toFixed(2)}`
}

const getInstructorInitials = (name) => {
  if (!name) return 'NN'
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
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
  if (!minutes || minutes === 0) return '0m'
  if (minutes < 60) {
    return `${minutes}m`
  }
  const hours = Math.floor(minutes / 60)
  const remainingMinutes = minutes % 60
  return remainingMinutes > 0 ? `${hours}h ${remainingMinutes}m` : `${hours}h`
}

const getDifficultyBadgeClass = (level) => {
  const classes = {
    beginner: 'bg-green-900/30 text-green-400',
    intermediate: 'bg-yellow-900/30 text-yellow-400',
    advanced: 'bg-red-900/30 text-red-400',
  }
  return classes[level] || 'bg-dark-700 text-dark-200'
}

const translateDifficulty = (level) => {
  const translations = {
    beginner: 'Začiatočník',
    intermediate: 'Stredne pokročilý',
    advanced: 'Pokročilý',
  }
  return translations[level] || level
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
