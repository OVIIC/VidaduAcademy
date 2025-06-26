<template>
  <div class="card hover:shadow-lg transition-shadow duration-300 group">
    <div class="relative overflow-hidden">
      <img
        :src="course.thumbnail || '/placeholder-course.jpg'"
        :alt="course.title"
        class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
      />
      <div class="absolute top-3 left-3">
        <span
          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white"
          :style="{ backgroundColor: course.category?.color || '#3B82F6' }"
        >
          {{ course.category?.name }}
        </span>
      </div>
      <div v-if="course.featured" class="absolute top-3 right-3">
        <StarIcon class="h-5 w-5 text-yellow-400 fill-current" />
      </div>
    </div>

    <div class="p-6">
      <!-- Instructor -->
      <div class="flex items-center mb-3">
        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
          <span class="text-xs font-medium text-gray-600">
            {{ getInstructorInitials(course.instructor?.name) }}
          </span>
        </div>
        <div>
          <p class="text-sm font-medium text-gray-900">{{ course.instructor?.name }}</p>
          <p class="text-xs text-gray-500">
            {{ formatSubscribers(course.instructor?.subscribers_count) }} odberateľov
          </p>
        </div>
      </div>

      <!-- Course Title -->
      <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-primary-600 transition-colors">
        {{ course.title }}
      </h3>

      <!-- Short Description -->
      <p class="text-gray-600 text-sm mb-4 line-clamp-2">
        {{ course.short_description }}
      </p>

      <!-- Course Meta -->
      <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
        <div class="flex items-center space-x-4">
          <div class="flex items-center">
            <ClockIcon class="h-4 w-4 mr-1" />
            {{ formatDuration(course.duration_minutes) }}
          </div>
          <div class="flex items-center">
            <AcademicCapIcon class="h-4 w-4 mr-1" />
            {{ course.total_lessons }} lekcií
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
          <span class="text-2xl font-bold text-gray-900">
            ${{ course.formatted_price }}
          </span>
          <span class="text-sm text-gray-500 ml-1">{{ course.currency }}</span>
        </div>
        
        <router-link
          :to="{ name: 'CourseDetail', params: { slug: course.slug } }"
          class="btn-primary"
        >
          Zistiť viac
        </router-link>
      </div>

      <!-- Enrollment Count (if available) -->
      <div v-if="course.enrollments_count" class="mt-3 pt-3 border-t border-gray-100">
        <div class="flex items-center text-sm text-gray-500">
          <UsersIcon class="h-4 w-4 mr-1" />
          {{ course.enrollments_count }} zapísaných študentov
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  StarIcon,
  ClockIcon,
  AcademicCapIcon,
  UsersIcon,
} from '@heroicons/vue/24/outline'

defineProps({
  course: {
    type: Object,
    required: true,
  },
})

const getInstructorInitials = (name) => {
  if (!name) return ''
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const formatSubscribers = (count) => {
  if (count >= 1000000) {
    return `${(count / 1000000).toFixed(1)}M`
  } else if (count >= 1000) {
    return `${(count / 1000).toFixed(1)}K`
  }
  return count?.toString() || '0'
}

const formatDuration = (minutes) => {
  if (minutes < 60) {
    return `${minutes}m`
  }
  const hours = Math.floor(minutes / 60)
  const remainingMinutes = minutes % 60
  return remainingMinutes > 0 ? `${hours}h ${remainingMinutes}m` : `${hours}h`
}

const getDifficultyBadgeClass = (level) => {
  const classes = {
    beginner: 'bg-green-100 text-green-800',
    intermediate: 'bg-yellow-100 text-yellow-800',
    advanced: 'bg-red-100 text-red-800',
  }
  return classes[level] || 'bg-gray-100 text-gray-800'
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
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
